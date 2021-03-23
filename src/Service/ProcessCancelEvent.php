<?php

namespace App\Service;

use App\Entity\Event;
use App\Entity\Status;
use App\Entity\Subscription;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use function Symfony\Component\String\u;

class ProcessCancelEvent extends AbstractController
{
    public function cancelEvent($data)
    {
        $event = $this->getDoctrine()
            ->getRepository(Event::class)
            ->findOneBy(['name' => $data['event']]);
        $state = $this->getDoctrine()
            ->getRepository(Status::class)
            ->findOneBy(['state' => 'Cancelled']);
        $event->setStatus($state);
        $cancelReason = u(': ')->join(["Event has been cancelled due to the following reasons", $data['reason']]);
        $event->setDescription($cancelReason);

        $entityManager = $this->getDoctrine()->getManager();
        $subscriptionList = $this->getDoctrine()
            ->getRepository(Subscription::class)
            ->findBy(['event' => $event]);
        foreach ($subscriptionList as $subscription) {
            $entityManager->remove($subscription);
        }
        $entityManager->persist($event);
        $entityManager->flush();
    }
}