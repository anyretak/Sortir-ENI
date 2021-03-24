<?php

namespace App\Service;

use App\Entity\Campus;
use App\Entity\Status;
use App\Entity\Subscription;
use DateInterval;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class ProcessFilters implements ProcessFiltersInterface
{

    private Security $security;
    private EntityManagerInterface $entityManager;

    public function __construct(Security $security, EntityManagerInterface $entityManager)
    {
        $this->security = $security;
        $this->entityManager = $entityManager;
    }

    public function processFilters($filters, $userSubs = [])
    {
        $campus = $this->entityManager
            ->getRepository(Campus::class)
            ->findOneBy(['name' => $filters->getCampusName()]);
        $filters->setCampus($campus);

        if ($filters->getUserName()) {
            $user = $this->security->getUser();
            $filters->setUser($user);
        }

        if ($filters->getPast()) {
            $status = $this->entityManager
                ->getRepository(Status::class)
                ->findOneBy(['state' => 'Finished']);
            $filters->setStatus($status);
        }

        if ($filters->getUserSub() || $filters->getUserNonsub()) {
            $userName = $this->security->getUser();
            $userSubList = $this->entityManager
                ->getRepository(Subscription::class)
                ->findBy(['user' => $userName]);
            foreach ($userSubList as $sub) {
                $sub = $sub->getEvent()->getName();
                $userSubs [] = $sub;
            }
            $filters->setEventNames($userSubs);
        }

        return $filters;
    }

    public function archiveDate()
    {
        $date = new DateTime();
        $date->getTimestamp();
        $date->sub(new DateInterval('P1M'));
        return $date;
    }
}