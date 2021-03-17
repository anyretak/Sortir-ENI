<?php

namespace App\Service;

use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Security;

class ProcessHome extends AbstractController
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function processMobileHome(ProcessFilters $processFilters, EventRepository $eventRepository)
    {
        $date = $processFilters->archiveDate();
        /** @var \App\Entity\User $user */
        $user = $this->security->getUser();
        $campus = $user->getCampus();
        return $eventRepository->filterMobile($date, $campus);
    }

    public function processHome(ProcessFilters $processFilters, EventRepository $eventRepository)
    {
        $date = $processFilters->archiveDate();
        return $eventRepository->filterArchive($date);
    }
}