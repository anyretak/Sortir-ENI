<?php

namespace App\Service;

use App\Entity\Event;
use App\Entity\Status;
use App\Entity\Subscription;
use Doctrine\ORM\EntityManagerInterface;
use function Symfony\Component\String\u;

class ProcessCancelEvent implements ProcessCancelEventInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function cancelEvent($data)
    {
        $event = $this->entityManager
            ->getRepository(Event::class)
            ->findOneBy(['name' => $data['event']]);
        $state = $this->entityManager
            ->getRepository(Status::class)
            ->findOneBy(['state' => 'Cancelled']);
        $event->setStatus($state);
        $cancelReason = u(': ')->join(["Event has been cancelled due to the following reasons", $data['reason']]);
        $event->setDescription($cancelReason);

        $entityManager = $this->entityManager;
        $subscriptionList = $this->entityManager
            ->getRepository(Subscription::class)
            ->findBy(['event' => $event]);
        foreach ($subscriptionList as $subscription) {
            $entityManager->remove($subscription);
        }
        $entityManager->persist($event);
        $entityManager->flush();
    }
}