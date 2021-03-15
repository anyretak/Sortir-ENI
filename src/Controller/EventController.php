<?php

namespace App\Controller;

use App\Form\EventType;
use App\Repository\CampusRepository;
use App\Repository\CityRepository;
use App\Repository\EventRepository;
use App\Repository\LocationRepository;
use App\Repository\StatusRepository;
use App\Repository\SubscriptionRepository;
use App\Repository\UserRepository;
use Mobile_Detect;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use function Symfony\Component\String\u;


class EventController extends AbstractController
{
    //******************************************************************//
    //****************************NEW EVENT*****************************//
    //******************************************************************//
    #[Route('/new_event', name: 'event')]
    public function newEvent(Request $request, UserRepository $userRepository, StatusRepository $statusRepository, CityRepository $cityRepository, CampusRepository $campusRepository, LocationRepository $locationRepository): Response
    {
        $user = $this->getUser();
        $user = $userRepository->findOneBy(['username' => $user->getUsername()]);
        $userCampus = $user->getCampus();
        $campus = $campusRepository->findAll();
        $city = $cityRepository->findAll();
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

    #[Route('/api/location', name: 'api_location')]
    public function ajaxLocation(Request $request, LocationRepository $locationRepository, SerializerInterface $serializer): Response
    {
        $data = $request->toArray();
        $location = $data['locId'];
        $location = $locationRepository->findOneBy(['name' => $location]);
        $locationJson = $serializer->serialize($location, 'json', ['groups' => ['location']]);

        return new JsonResponse($locationJson, Response::HTTP_OK, [], true);
    }

    #[Route('/api/city', name: 'api_city')]
    public function ajaxCity(Request $request, CityRepository $cityRepository, SerializerInterface $serializer): Response
    {
        $data = $request->toArray();
        $city = $data['cityId'];
        $city = $cityRepository->findOneBy(['name' => $city]);
        $cityJson = $serializer->serialize($city, 'json', ['groups' => ['city']]);

        return new JsonResponse($cityJson, Response::HTTP_OK, [], true);
    }

    #[Route('/api/location_filter', name: 'api_location_filter')]
    public function ajaxLocationFilter(Request $request, LocationRepository $locationRepository, CityRepository $cityRepository, SerializerInterface $serializer): Response
    {
        $data = $request->toArray();
        $city = $data['cityId'];
        $city = $cityRepository->findOneBy(['name' => $city]);
        $location = $locationRepository->findBy(['city' => $city]);
        $locationJson = $serializer->serialize($location, 'json', ['groups' => ['location']]);
        return new JsonResponse($locationJson, Response::HTTP_OK, [], true);
    }

    //******************************************************************//
    //*************************EVENT MANAGEMENT*************************//
    //******************************************************************//
    #[Route('/event_details/{event}', name: 'event_details')]
    public function eventDetails($event, EventRepository $eventRepository, $userDetails = [])
    {
        $eventDetails = $eventRepository->findOneBy(['name' => $event]);
        $locationDetails = $eventDetails->getLocation();
        $cityDetails = $eventDetails->getLocation()->getCity();
        $subscriptionDetails = $eventDetails->getSubscriptions();
        foreach ($subscriptionDetails as $detail) {
            $user = $detail->getUser();
            $userDetails[] = $user;
        }

        $detect = new Mobile_Detect;
        if ($detect->isMobile() && !$detect->isTablet()) {
            return $this->render('event/mobile_event_details.html.twig', [
                'eventDetails' => $eventDetails,
                'locationDetails' => $locationDetails,
                'cityDetails' => $cityDetails,
                'userDetails' => $userDetails,
            ]);
        }
        return $this->render('event/event_details.html.twig', [
            'eventDetails' => $eventDetails,
            'locationDetails' => $locationDetails,
            'cityDetails' => $cityDetails,
            'userDetails' => $userDetails,
        ]);
    }

    #[Route('/edit_event/{event}', name: 'edit_event')]
    public function editEvent($event, Request $request, EventRepository $eventRepository, StatusRepository $statusRepository): Response
    {
        $event = $eventRepository->findOneBy(['name' => $event]);
        $location = $event->getLocation();
        $city = $location->getCity();

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

    //******************************************************************//
    //**************************CANCEL EVENT****************************//
    //******************************************************************//
    #[Route('/cancel_event/{event}', name: 'cancel_event')]
    public function cancelEvent($event, EventRepository $eventRepository): Response
    {
        $event = $eventRepository->findOneBy(['name' => $event]);
        $location = $event->getLocation();
        $city = $location->getCity();

        return $this->render('event/cancel_event.html.twig', [
            'event' => $event,
            'location' => $location,
            'city' => $city,
        ]);
    }

    #[Route('/api/cancel_event', name: 'api_cancel_event')]
    public function ajaxCancelEvent(Request $request, EventRepository $eventRepository, StatusRepository $statusRepository, SubscriptionRepository $subscriptionRepository): Response
    {
        $data = $request->toArray();
        $reason = $data['reason'];
        $event = $data['event'];

        $cancelReason = u(': ')->join(["Event has been cancelled due to the following reasons", $reason]);
        $event = $eventRepository->findOneBy(['name' => $event]);
        $state = $statusRepository->findOneBy(['state' => 'Cancelled']);

        $event->setStatus($state);
        $event->setDescription($cancelReason);

        $entityManager = $this->getDoctrine()->getManager();
        $subscriptionList = $subscriptionRepository->findBy(['event' => $event]);
        foreach ($subscriptionList as $subscription) {
            $entityManager->remove($subscription);
            $entityManager->flush();
        }

        $entityManager->persist($event);
        $entityManager->flush();

        return new Response();
    }
}
