<?php

namespace App\Service;

use App\Entity\Event;
use App\Entity\Subscription;
use App\Entity\User;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class ProcessSub implements ProcessSubInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function processSub($data)
    {
        $user = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['name' => $data['user']]);
        $event = $this->entityManager
            ->getRepository(Event::class)
            ->findOneBy(['name' => $data['event']]);
        $subscription = $this->entityManager
            ->getRepository(Subscription::class)
            ->findOneBy(['user' => $user, 'event' => $event]);
        $entityManager = $this->entityManager;
        if (is_null($subscription)) {
            $subscription = new Subscription();
            $date = new DateTime();
            $date->getTimestamp();
            $subscription->setDate($date);
            $subscription->setUser($user);
            $subscription->setEvent($event);
            $entityManager->persist($subscription);
        } else {
            $entityManager->remove($subscription);
        }
        $entityManager->flush();
    }
}