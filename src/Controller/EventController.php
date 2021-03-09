<?php

namespace App\Controller;

use App\Form\CancelEventType;
use App\Form\EventType;
use App\Repository\CampusRepository;
use App\Repository\CityRepository;
use App\Repository\EventRepository;
use App\Repository\LocationRepository;
use App\Repository\StatusRepository;
use App\Repository\SubscriptionRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class EventController extends AbstractController
{
    #[Route('/new_event', name: 'event')]
    public function newEvent(Request $request, UserRepository $userRepository, StatusRepository $statusRepository, CityRepository $cityRepository, CampusRepository $campusRepository, LocationRepository $locationRepository): Response
    {
        $user = $this->getUser();
        $user = $userRepository->findOneBy(['username' => $user->getUsername()]);
        $userCampus = $user->getCampus();
        $city = $cityRepository->findAll();
        $campus = $campusRepository->findAll();
        $location = $locationRepository->findAll();

        $form = $this->createForm(EventType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($form->getClickedButton() === $form->get('create')) {
                $state = $statusRepository->findOneBy(['state' => 'Created']);
                $flashMessage = 'Success! New event was created. Do you want to publish it now?';
            }

            if ($form->getClickedButton() === $form->get('publish')) {
                $state = $statusRepository->findOneBy(['state' => 'Open']);
                $flashMessage = 'Success! New event was created and it is now open!';
            }

            $entityManager = $this->getDoctrine()->getManager();
            $event = $form->getData();
            $event->setStatus($state);
            $event->setUser($user);
            $event->setCampus($userCampus);
            $entityManager->persist($event);
            $entityManager->flush();

            $this->addFlash(
                'notice',
                $flashMessage,
            );

            return $this->redirectToRoute('home');
        }

        return $this->render('event/new_event.html.twig', [
            'form' => $form->createView(),
            'city' => $city,
            'campus' => $campus,
            'userCampus' => $userCampus,
            'location' => $location,
        ]);
    }

    #[Route('/event_details/{event}', name: 'event_details')]
    public function eventDetails($event, EventRepository $eventRepository, $userDetails = [])
    {
        $eventDetails = $eventRepository->findOneBy(['name' => $event]);
        $locationDetails = $eventDetails->getLocation();
        $subscriptionDetails = $eventDetails->getSubscriptions();

        foreach ($subscriptionDetails as $detail) {
            $user = $detail->getUser();
            $userDetails[] = $user;
        }

        return $this->render('event/event_details.html.twig', [
            'eventDetails' => $eventDetails,
            'locationDetails' => $locationDetails,
            'userDetails' => $userDetails,
        ]);
    }

    #[Route('/edit_event/{event}', name: 'edit_event')]
    public function editEvent($event, Request $request, EventRepository $eventRepository, StatusRepository $statusRepository): Response
    {
        $event = $eventRepository->findOneBy(['name' => $event]);
        $location = $event -> getLocation();
        $city = $location -> getCity();

        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($form->getClickedButton() === $form->get('create')) {
                $state = $statusRepository->findOneBy(['state' => 'Created']);
                $flashMessage = 'Success! Event was modified. Do you want to publish it now?';
            }

            if ($form->getClickedButton() === $form->get('publish')) {
                $state = $statusRepository->findOneBy(['state' => 'Open']);
                $flashMessage = 'Success! Your event was published and it is now open!';
            }

            $entityManager = $this->getDoctrine()->getManager();
            $event = $form->getData();
            $event->setStatus($state);
            $entityManager->flush();

            $this->addFlash(
                'notice',
                $flashMessage,
            );

            return $this->redirectToRoute('home');
        }

        return $this->render('event/edit_event.html.twig', [
            'form' => $form->createView(),
            'event' => $event,
            'location' => $location,
            'city' => $city,
        ]);
    }

    #[Route('/cancel_event/{event}', name: 'cancel_event')]
    public function cancelEvent($event, EventRepository $eventRepository): Response
    {
        $event = $eventRepository->findOneBy(['name' => $event]);
        $location = $event->getLocation();
        $city = $location->getCity();

        return $this->render('event/cancel_event.html.twig', [
            'event' => $event,
            'location' => $location,
            'city'=> $city,
        ]);
    }
}
