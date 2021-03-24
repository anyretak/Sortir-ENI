<?php

namespace App\Controller;

use App\Form\EventType;
use App\Repository\CampusRepository;
use App\Repository\CityRepository;
use App\Repository\EventRepository;
use App\Repository\LocationRepository;
use App\Repository\StatusRepository;
use App\Repository\UserRepository;
use App\Service\ProcessCancelEventInterface;
use Mobile_Detect;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class EventController extends AbstractController
{
    private CityRepository $cityRepository;
    private CampusRepository $campusRepository;
    private UserRepository $userRepository;
    private StatusRepository $statusRepository;
    private LocationRepository $locationRepository;
    private EventRepository $eventRepository;
    private SerializerInterface $serializer;
    private ProcessCancelEventInterface $processCancelEvent;

    public function __construct(CityRepository $cityRepository, EventRepository $eventRepository,
                                CampusRepository $campusRepository, UserRepository $userRepository,
                                StatusRepository $statusRepository, LocationRepository $locationRepository,
                                ProcessCancelEventInterface $processCancelEvent, SerializerInterface $serializer)
    {
        $this->cityRepository = $cityRepository;
        $this->campusRepository = $campusRepository;
        $this->userRepository = $userRepository;
        $this->statusRepository = $statusRepository;
        $this->locationRepository = $locationRepository;
        $this->eventRepository = $eventRepository;
        $this->serializer = $serializer;
        $this->processCancelEvent = $processCancelEvent;
    }

    //****************************NEW EVENT*****************************//
    #[Route('/new_event', name: 'event')]
    public function newEvent(Request $request): Response
    {
        $user = $this->getUser();
        $user = $this->userRepository->findOneBy(['username' => $user->getUsername()]);
        $userCampus = $user->getCampus();
        $campus = $this->campusRepository->findAll();
        $city = $this->cityRepository->findAll();
        $location = $this->locationRepository->findAll();

        $form = $this->createForm(EventType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            if ($form->getClickedButton() === $form->get('create')) {
                $state = $this->statusRepository->findOneBy(['state' => 'Created']);
                $flashMessage = 'Success! New event was created. Do you want to publish it now?';
            }

            if ($form->getClickedButton() === $form->get('publish')) {
                $state = $this->statusRepository->findOneBy(['state' => 'Open']);
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
    public function ajaxLocation(Request $request): Response
    {
        $data = $request->toArray();
        $location = $data['locId'];
        $location = $this->locationRepository->findOneBy(['name' => $location]);
        $locationJson = $this->serializer->serialize($location, 'json', ['groups' => ['location']]);

        return new JsonResponse($locationJson, Response::HTTP_OK, [], true);
    }

    #[Route('/api/city', name: 'api_city')]
    public function ajaxCity(Request $request): Response
    {
        $data = $request->toArray();
        $city = $data['cityId'];
        $city = $this->cityRepository->findOneBy(['name' => $city]);
        $cityJson = $this->serializer->serialize($city, 'json', ['groups' => ['city']]);

        return new JsonResponse($cityJson, Response::HTTP_OK, [], true);
    }

    #[Route('/api/location_filter', name: 'api_location_filter')]
    public function ajaxLocationFilter(Request $request): Response
    {
        $data = $request->toArray();
        $city = $data['cityId'];
        $city = $this->cityRepository->findOneBy(['name' => $city]);
        $location = $this->locationRepository->findBy(['city' => $city]);
        $locationJson = $this->serializer->serialize($location, 'json', ['groups' => ['location']]);
        return new JsonResponse($locationJson, Response::HTTP_OK, [], true);
    }

    //*************************EVENT MANAGEMENT*************************//
    #[Route('/event_details/{event}', name: 'event_details')]
    public function eventDetails($event, $userDetails = [])
    {
        $eventDetails = $this->eventRepository->findOneBy(['name' => $event]);
        $locationDetails = $eventDetails->getLocation();
        $cityDetails = $locationDetails->getCity();
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
    public function editEvent($event, Request $request): Response
    {
        $event = $this->eventRepository->findOneBy(['name' => $event]);
        $location = $event->getLocation();
        $city = $location->getCity();

        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            if ($form->getClickedButton() === $form->get('create')) {
                $state = $this->statusRepository->findOneBy(['state' => 'Created']);
                $flashMessage = 'Success! Event was modified. Do you want to publish it now?';
            }

            if ($form->getClickedButton() === $form->get('publish')) {
                $state = $this->statusRepository->findOneBy(['state' => 'Open']);
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

    //**************************CANCEL EVENT****************************//
    #[Route('/cancel_event/{event}', name: 'cancel_event')]
    public function cancelEvent($event): Response
    {
        $event = $this->eventRepository->findOneBy(['name' => $event]);
        $location = $event->getLocation();
        $city = $location->getCity();
        return $this->render('event/cancel_event.html.twig', [
            'event' => $event,
            'location' => $location,
            'city' => $city,
        ]);
    }

    #[Route('/api/cancel_event', name: 'api_cancel_event')]
    public function ajaxCancelEvent(Request $request): Response
    {
        $data = $request->toArray();
        $this->processCancelEvent->cancelEvent($data);
        return new Response();
    }
}
