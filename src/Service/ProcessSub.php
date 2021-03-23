<?php

namespace App\Service;

use App\Entity\Event;
use App\Entity\Subscription;
use App\Entity\User;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProcessSub extends AbstractController
{
    public function processSub($data)
    {
        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->findOneBy(['name' => $data['user']]);
        $event = $this->getDoctrine()
            ->getRepository(Event::class)
            ->findOneBy(['name' => $data['event']]);
        $subscription = $this->getDoctrine()
            ->getRepository(Subscription::class)
            ->findOneBy(['user' => $user, 'event' => $event]);
        $entityManager = $this->getDoctrine()->getManager();
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