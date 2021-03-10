<?php

namespace App\Controller;

use App\Repository\CampusRepository;
use App\Repository\EventRepository;
use App\Repository\StatusRepository;
use DateInterval;
use DateTime;
use DateTimeZone;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(Request $request, EventRepository $eventRepository, CampusRepository $campusRepository, StatusRepository $statusRepository): Response
    {
        $listEvents = $eventRepository->findAll();
        $campusList = $campusRepository->findAll();
        $stateClosed = $statusRepository->findOneBy(['state' => 'Closed']);
        $stateFinished = $statusRepository->findOneBy(['state' => 'Finished']);
        $stateActive = $statusRepository->findOneBy(['state' => 'Active']);
        $date = new DateTime();
        /*$date = new DateTime('now', new DateTimeZone('Europe/Paris'));*/
        $date->getTimestamp();
        dump($date);

        foreach ($listEvents as $event) {
            $eventStatus = $event->getStatus()->getState();
            $eventDate = $event->getDate();
            $eventDuration = clone $eventDate;
            $eventLimitDate = $event->getLimitDate();
            $timeInterval = $event->getDuration();
            $interval = new DateInterval('PT0H' . $timeInterval . 'M');
            $eventDuration->add($interval);

            if ($eventStatus !== 'Cancelled') {
                if ($eventLimitDate > $date and $eventDate > $date) {
                    ;
                } else if ($eventLimitDate < $date and $eventDate > $date) {
                    $event->setStatus($stateClosed);
                } else if ($eventLimitDate < $date and $eventDate < $date) {
                    if ($eventDuration > $date) {
                        $event->setStatus($stateActive);
                    } else {
                        $event->setStatus($stateFinished);
                    }
                }
            }
            $eventList[] = $event;
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($event);
            $entityManager->flush();
        }

        return $this->render('home/index.html.twig', [
            'eventList' => $eventList,
            'campusList' => $campusList,
        ]);
    }
}

