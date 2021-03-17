<?php

namespace App\Service;

use App\Entity\Subscription;
use App\Repository\EventRepository;
use App\Repository\SubscriptionRepository;
use App\Repository\UserRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProcessSub extends AbstractController
{
    public function processSub($data, UserRepository $userRepository, EventRepository $eventRepository, SubscriptionRepository $subscriptionRepository)
    {
        $user = $userRepository->findOneBy(['name' => $data['user']]);
        $event = $eventRepository->findOneBy(['name' => $data['event']]);
        $subscription = $subscriptionRepository->findOneBy(['user' => $user, 'event' => $event]);
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