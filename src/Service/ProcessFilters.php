<?php

namespace App\Service;

use App\Repository\CampusRepository;
use App\Repository\StatusRepository;
use App\Repository\SubscriptionRepository;
use DateInterval;
use DateTime;
use Symfony\Component\Security\Core\Security;

class ProcessFilters
{

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function processFilters($filters, CampusRepository $campusRepository, StatusRepository $statusRepository, SubscriptionRepository $subscriptionRepository, $userSubs = [])
    {
        $campus = $campusRepository->findOneBy(['name' => $filters->getCampusName()]);
        $filters->setCampus($campus);

        if ($filters->getUserName()) {
            $user = $this->security->getUser();
            $filters->setUser($user);
        }

        if ($filters->getPast()) {
            $status = $statusRepository->findOneBy(['state' => 'Finished']);
            $filters->setStatus($status);
        }

        if ($filters->getUserSub() || $filters->getUserNonsub()) {
            $userName = $this->security->getUser();
            $userSubList = $subscriptionRepository->findBy(['user' => $userName]);
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