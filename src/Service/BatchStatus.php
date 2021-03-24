<?php

namespace App\Service;

use App\Entity\Event;
use App\Entity\Status;
use DateInterval;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class BatchStatus
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    public function __construct (EntityManagerInterface $entityManager) {

        $this->entityManager = $entityManager;
    }

    public function batchStatus($eventList = [])
    {
        $date = new DateTime();
        $date->getTimestamp();
        $dateNow = clone $date;
        $date->sub(new DateInterval('P1M'));

        $stateClosed = $this->entityManager
            ->getRepository(Status::class)
            ->findOneBy(['state' => 'Closed']);
        $stateFinished = $this->entityManager
            ->getRepository(Status::class)
            ->findOneBy(['state' => 'Finished']);
        $stateActive = $this->entityManager
            ->getRepository(Status::class)
            ->findOneBy(['state' => 'Active']);
        $listEvents = $this->entityManager
            ->getRepository(Event::class)
            ->filterArchive($date);
        /*$entityManager = $this->entityManager;*/

        foreach ($listEvents as $event) {
            $eventStatus = $event->getStatus()->getState();
            $eventDate = $event->getDate();
            $eventLimitDate = $event->getLimitDate();
            $timeInterval = $event->getDuration();
            $eventDuration = clone $eventDate;
            $eventDuration->add(new DateInterval('PT0H' . $timeInterval . 'M'));
            if ($eventStatus !== 'Cancelled') {
                if ($eventLimitDate > $dateNow and $eventDate > $dateNow) {
                    ;
                } else if ($eventLimitDate < $dateNow and $eventDate > $dateNow) {
                    $event->setStatus($stateClosed);
                } else if ($eventLimitDate < $dateNow and $eventDate < $dateNow) {
                    if ($eventDuration > $dateNow) {
                        $event->setStatus($stateActive);
                    } else {
                        $event->setStatus($stateFinished);
                    }
                }
            }
            $eventList[] = $event;
            $this->entityManager->persist($event);
        }
        //AVOIDS flush every time in the loop
        $this->entityManager->flush();
    }
}