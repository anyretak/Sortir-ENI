<?php

namespace App\Service;

use App\Repository\EventRepository;

interface ProcessHomeInterface
{
    public function processMobileHome(ProcessFiltersInterface $processFilters, EventRepository $eventRepository);

    public function processHome(ProcessFiltersInterface $processFilters, EventRepository $eventRepository);
}