<?php

namespace App\Controller;

use App\Entity\Subscription;
use App\Repository\CampusRepository;
use App\Repository\EventRepository;
use App\Repository\SubscriptionRepository;
use App\Repository\UserRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(Request $request, EventRepository $eventRepository, CampusRepository $campusRepository): Response
    {
        $listEvents = $eventRepository->findAll();
        $campusList = $campusRepository->findAll();

        return $this->render('home/index.html.twig', [
            'eventList' => $listEvents,
            'campusList' => $campusList,
        ]);
    }

    #[Route('/subscribe/{event}/{user}', name: 'subscribe')]
    public function subscribe($user, $event, UserRepository $userRepository, EventRepository $eventRepository): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $subscription = new Subscription();

        $date = new DateTime();
        $date->getTimestamp();
        $subscription->setDate($date);

        $user = $userRepository->findOneBy(['name' => $user]);
        $subscription->setUser($user);

        $event = $eventRepository->findOneBy(['name' => $event]);
        $subscription->setEvent($event);

        $entityManager->persist($subscription);
        $entityManager->flush();

        return $this->render('home/temp.html.twig');
    }

    #[Route('/remove/{event}/{user}', name: 'remove')]
    public function remove($user, $event, UserRepository $userRepository, EventRepository $eventRepository, SubscriptionRepository $subscriptionRepository): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $user = $userRepository->findOneBy(['name' => $user]);
        $event = $eventRepository->findOneBy(['name' => $event]);
        $subscription = $subscriptionRepository->findOneBy(['user' => $user, 'event' => $event]);

        $entityManager->remove($subscription);
        $entityManager->flush();

        return $this->render('home/temp.html.twig');
    }


}

