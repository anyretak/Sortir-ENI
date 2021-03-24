<?php

namespace App\Service;

use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class ProcessHome implements ProcessHomeInterface
{
    private Security $security;
    private EntityManagerInterface $entityManager;

    public function __construct(Security $security, EntityManagerInterface $entityManager)
    {
        $this->security = $security;
        $this->entityManager = $entityManager;
    }

    public function processMobileHome(ProcessFiltersInterface $processFilters, EventRepository $eventRepository)
    {
        $date = $processFilters->archiveDate();
        /** @var \App\Entity\User $user */
        $user = $this->security->getUser();
        $campus = $user->getCampus();
        return $eventRepository->filterMobile($date, $campus);
    }

    public function processHome(ProcessFiltersInterface $processFilters, EventRepository $eventRepository)
    {
        $date = $processFilters->archiveDate();
        return $eventRepository->filterArchive($date);
    }
}