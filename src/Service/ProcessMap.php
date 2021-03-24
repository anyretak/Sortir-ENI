<?php

namespace App\Service;

use App\Entity\Campus;
use App\Entity\Event;
use Doctrine\ORM\EntityManagerInterface;

class ProcessMap implements ProcessMapInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function processMap ($data, ProcessFiltersInterface $processFilters) {
        $eventCoords = [];
        $date = $processFilters->archiveDate();
        $campus = $this->entityManager
            ->getRepository(Campus::class)
            -> findOneBy(['name' => $data['campus']]);
        $events = $this->entityManager
            ->getRepository(Event::class)
            ->filterMobile($date, $campus);

        foreach ($events as $event) {
            $name = $event->getName();
            $lat = $event->getLocation()->getLatitude();
            $long = $event->getLocation()->getLongitude();
            $eventCoords[] = ['name' => $name, 'lat' => $lat, 'long' => $long];
        }
        return $eventCoords;
    }
}