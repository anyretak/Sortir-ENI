<?php

namespace App\Service;

use App\Entity\Campus;
use App\Entity\Status;
use App\Entity\Subscription;
use DateInterval;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Security;

class ProcessFilters extends AbstractController
{

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function processFilters($filters, $userSubs = [])
    {
        $campus = $this->getDoctrine()
            ->getRepository(Campus::class)
            ->findOneBy(['name' => $filters->getCampusName()]);
        $filters->setCampus($campus);

        if ($filters->getUserName()) {
            $user = $this->security->getUser();
            $filters->setUser($user);
        }

        if ($filters->getPast()) {
            $status = $this->getDoctrine()
                ->getRepository(Status::class)
                ->findOneBy(['state' => 'Finished']);
            $filters->setStatus($status);
        }

        if ($filters->getUserSub() || $filters->getUserNonsub()) {
            $userName = $this->security->getUser();
            $userSubList = $this->getDoctrine()
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