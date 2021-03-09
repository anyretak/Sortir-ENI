<?php

namespace App\Controller;

use App\Entity\Subscription;
use App\Repository\CampusRepository;
use App\Repository\CityRepository;
use App\Repository\EventRepository;
use App\Repository\LocationRepository;
use App\Repository\StatusRepository;
use App\Repository\SubscriptionRepository;
use App\Repository\UserRepository;
use DateTime;
use DateTimeZone;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use function Symfony\Component\String\u;

class AjaxController extends AbstractController
{
    #[Route('/ajax/city_event', name: 'ajax_city_event')]
    public function ajaxCityEvent(Request $request, CityRepository $cityRepository, SerializerInterface $serializer): Response
    {
        if ($request->isXmlHttpRequest()) {

            $eventData = $request->request->all();
            $propertyAccessor = PropertyAccess::createPropertyAccessor();
            $cityName = $propertyAccessor->getValue($eventData, '[city]');

            $city = $cityRepository->findOneBy(['name' => $cityName]);
            $cityJson = $serializer->serialize($city, 'json', ['groups' => ['city']]);

            return new Response($cityJson);
        }
    }

    #[Route('/ajax/location_event', name: 'ajax_location_event')]
    public function ajaxLocationEvent(Request $request, LocationRepository $locationRepository, SerializerInterface $serializer): Response
    {
        if ($request->isXmlHttpRequest()) {

            $eventData = $request->request->all();
            $propertyAccessor = PropertyAccess::createPropertyAccessor();
            $locId = $propertyAccessor->getValue($eventData, '[locId]');

            $location = $locationRepository->find($locId);
            $locationJson = $serializer->serialize($location, 'json', ['groups' => ['location']]);

            return new Response($locationJson);
        }
    }

    #[Route('/ajax/user_sub', name: 'ajax_user_sub')]
    public function ajaxUserSub(Request $request, UserRepository $userRepository, EventRepository $eventRepository, SerializerInterface $serializer, CampusRepository $campusRepository): Response
    {
        if ($request->isXmlHttpRequest()) {

            $eventData = $request->request->all();
            $propertyAccessor = PropertyAccess::createPropertyAccessor();
            $user = $propertyAccessor->getValue($eventData, '[user]');
            $event = $propertyAccessor->getValue($eventData, '[event]');

            $subscription = new Subscription();
            $date = new DateTime();
            $date->getTimestamp();
            $subscription->setDate($date);
            $user = $userRepository->findOneBy(['name' => $user]);
            $subscription->setUser($user);
            $event = $eventRepository->findOneBy(['name' => $event]);
            $subscription->setEvent($event);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($subscription);
            $entityManager->flush();

            $listEvents = $eventRepository->findAll();
            /*$campusList = $campusRepository->findAll();*/

            return $this->render('templates/_table.html.twig', [
                'eventList' => $listEvents,
                /*'campusList' => $campusList,*/
            ]);
        }
    }

    #[Route('/ajax/user_unsub', name: 'ajax_user_unsub')]
    public function ajaxUserUnsub(Request $request, UserRepository $userRepository, EventRepository $eventRepository, CampusRepository $campusRepository, SubscriptionRepository $subscriptionRepository): Response
    {
        if ($request->isXmlHttpRequest()) {

            $eventData = $request->request->all();
            $propertyAccessor = PropertyAccess::createPropertyAccessor();
            $user = $propertyAccessor->getValue($eventData, '[user]');
            $event = $propertyAccessor->getValue($eventData, '[event]');

            $user = $userRepository->findOneBy(['name' => $user]);
            $event = $eventRepository->findOneBy(['name' => $event]);
            $subscription = $subscriptionRepository->findOneBy(['user' => $user, 'event' => $event]);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($subscription);
            $entityManager->flush();

            $listEvents = $eventRepository->findAll();
            /*$campusList = $campusRepository->findAll();*/

            return $this->render('templates/_table.html.twig', [
                'eventList' => $listEvents,
                /*'campusList' => $campusList,*/
            ]);
        }
    }

    #[Route('/ajax/cancel_event', name: 'ajax_cancel_event')]
    public function ajaxCancelEvent(Request $request, EventRepository $eventRepository, StatusRepository $statusRepository, SubscriptionRepository $subscriptionRepository): Response
    {
        if ($request->isXmlHttpRequest()) {

            $eventData = $request->request->all();
            $propertyAccessor = PropertyAccess::createPropertyAccessor();
            $reasonData = $propertyAccessor->getValue($eventData, '[reason]');
            $eventData = $propertyAccessor->getValue($eventData, '[event]');
            $cancelReason = u(': ')->join(["Event has been cancelled due to the following reasons", $reasonData]);

            $event = $eventRepository->findOneBy(['name' => $eventData]);
            $state = $statusRepository->findOneBy(['state' => 'Cancelled']);
            $event->setStatus($state);
            $event->setDescription($cancelReason);

            $subscriptionList = $subscriptionRepository->findBy([ 'event' => $event]);
            $entityManager = $this->getDoctrine()->getManager();

            foreach ($subscriptionList as $subscription) {
                $entityManager->remove($subscription);
                $entityManager->flush();
            }

            $entityManager->persist($event);
            $entityManager->flush();

            $this->addFlash(
                'notice',
                'Your event has been cancelled.'
            );

            return new Response('Hello. Event has been cancelled');
        }
    }

    #[Route('/ajax/campus_filter', name: 'ajax_campus_filter')]
    public function ajaxCampusFilter(Request $request, UserRepository $userRepository, EventRepository $eventRepository, CampusRepository $campusRepository, SubscriptionRepository $subscriptionRepository): Response
    {
        if ($request->isXmlHttpRequest()) {

            $eventData = $request->request->all();
            $propertyAccessor = PropertyAccess::createPropertyAccessor();
            $campus = $propertyAccessor->getValue($eventData, '[campus]');

            if ($campus !== "") {
                $campus = $campusRepository->findOneBy(['name' => $campus]);
                $listEvents = $eventRepository->findBy(['campus' => $campus]);
            } else {
                $listEvents = $eventRepository->findAll();
            }

            return $this->render('templates/_table.html.twig', [
                'eventList' => $listEvents,
            ]);
        }
    }

    #[Route('/ajax/date_filter', name: 'ajax_date_filter')]
    public function ajaxDateFilter(Request $request, UserRepository $userRepository, EventRepository $eventRepository, CampusRepository $campusRepository, SubscriptionRepository $subscriptionRepository): Response
    {
/*        function convertMyDate($date): DateTime
        {
            $chunks = explode(' ', $date);
            $dateFormat = new \DateTime($chunks[1] . ' ' . $chunks[2] . ' ' . $chunks[3] . ' ' . $chunks[4]);
            $dateFormat->setTimezone(new DateTimeZone($chunks[5]));
            $dateFormat->format('d-m-Y H:i:s');
            return $dateFormat;
        }*/

        if ($request->isXmlHttpRequest()) {

            $eventData = $request->request->all();
            $propertyAccessor = PropertyAccess::createPropertyAccessor();
            $dateMin = $propertyAccessor->getValue($eventData, '[from]');
            $dateMax = $propertyAccessor->getValue($eventData, '[to]');

            if ($dateMin !== "" and $dateMax === "Invalid Date") {

                $chunksMin = explode(' ', $dateMin);
                $dateMinFormat = new \DateTime($chunksMin[1] . ' ' . $chunksMin[2] . ' ' . $chunksMin[3] . ' ' . $chunksMin[4]);
                $dateMinFormat->setTimezone(new DateTimeZone($chunksMin[5]));
                $dateMinFormat->format('d-m-Y H:i:s');

                $listEvents = $eventRepository->findByMinDate($dateMinFormat);

            } else if ($dateMin === "Invalid Date" and $dateMax !== "") {
                $chunksMax = explode(' ', $dateMax);
                $dateMaxFormat = new \DateTime($chunksMax[1] . ' ' . $chunksMax[2] . ' ' . $chunksMax[3] . ' ' . $chunksMax[4]);
                $dateMaxFormat->setTimezone(new DateTimeZone($chunksMax[5]));
                $dateMaxFormat->format('d-m-Y H:i:s');

                $listEvents = $eventRepository->findByMaxDate($dateMaxFormat);

            } else if ($dateMin !== "" and $dateMax !== "") {

                $chunksMin = explode(' ', $dateMin);
                $dateMinFormat = new \DateTime($chunksMin[1] . ' ' . $chunksMin[2] . ' ' . $chunksMin[3] . ' ' . $chunksMin[4]);
                $dateMinFormat->setTimezone(new DateTimeZone($chunksMin[5]));
                $dateMinFormat->format('d-m-Y H:i:s');

                $chunksMax = explode(' ', $dateMax);
                $dateMaxFormat = new \DateTime($chunksMax[1] . ' ' . $chunksMax[2] . ' ' . $chunksMax[3] . ' ' . $chunksMax[4]);
                $dateMaxFormat->setTimezone(new DateTimeZone($chunksMax[5]));
                $dateMaxFormat->format('d-m-Y H:i:s');

                $listEvents = $eventRepository->findByDate($dateMinFormat, $dateMaxFormat);

            } else {
                $listEvents = $eventRepository->findAll();
            }

            return $this->render('templates/_table.html.twig', [
                'eventList' => $listEvents,
            ]);
        }
    }
}
