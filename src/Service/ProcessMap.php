<?php

namespace App\Service;

use App\Entity\Campus;
use App\Entity\Event;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProcessMap extends AbstractController
{
    public function processMap ($data, ProcessFilters $processFilters) {
        $eventCoords = [];
        $date = $processFilters->archiveDate();
        $campus = $this->getDoctrine()
            ->getRepository(Campus::class)
            -> findOneBy(['name' => $data['campus']]);
        $events = $this->getDoctrine()
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