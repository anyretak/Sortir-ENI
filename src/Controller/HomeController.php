<?php

namespace App\Controller;

use app\Entity\User;
use App\Entity\Subscription;
use App\Repository\CampusRepository;
use App\Repository\EventRepository;
use App\Repository\StatusRepository;
use App\Repository\SubscriptionRepository;
use App\Repository\UserRepository;
use DateInterval;
use DateTime;
use Mobile_Detect;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    //******************************************************************//
    //**************************MAIN PAGE LOAD**************************//
    //******************************************************************//
    #[Route('/', name: 'home')]
    public function index(EventRepository $eventRepository, CampusRepository $campusRepository, StatusRepository $statusRepository, $eventList = []): Response
    {
        $detect = new Mobile_Detect;
        if ($detect->isMobile() && !$detect->isTablet()) {

            $date = new DateTime();
            $date->getTimestamp();
            $archiveDate = clone $date;
            $archiveDate->sub(new DateInterval('P1M'));

            /** @var \App\Entity\User $user */
            $user = $this->getUser();
            $campus = $user->getCampus();
            $listEvents = $eventRepository->filterMobile($archiveDate, $campus);

            return $this->render('home/home_mobile.html.twig', [
                'eventList' => $listEvents,
            ]);
        }

        $date = new DateTime();
        $date->getTimestamp();
        $archiveDate = clone $date;
        $archiveDate->sub(new DateInterval('P1M'));
        $listEvents = $eventRepository->filterArchive($archiveDate);
        $campusList = $campusRepository->findAll();
        $stateClosed = $statusRepository->findOneBy(['state' => 'Closed']);
        $stateFinished = $statusRepository->findOneBy(['state' => 'Finished']);
        $stateActive = $statusRepository->findOneBy(['state' => 'Active']);

        foreach ($listEvents as $event) {
            $eventStatus = $event->getStatus()->getState();
            $eventDate = $event->getDate();
            $eventDuration = clone $eventDate;
            $eventLimitDate = $event->getLimitDate();
            $timeInterval = $event->getDuration();
            $eventDuration->add(new DateInterval('PT0H' . $timeInterval . 'M'));
            if ($eventStatus !== 'Cancelled') {
                if ($eventLimitDate > $date and $eventDate > $date) {
                    ;
                } else if ($eventLimitDate < $date and $eventDate > $date) {
                    $event->setStatus($stateClosed);
                } else if ($eventLimitDate < $date and $eventDate < $date) {
                    if ($eventDuration > $date) {
                        $event->setStatus($stateActive);
                    } else {
                        $event->setStatus($stateFinished);
                    }
                }
            }
            $eventList[] = $event;
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($event);
            $entityManager->flush();
        }

        return $this->render('home/index.html.twig', [
            'eventList' => $eventList,
            'campusList' => $campusList,
        ]);
    }

    //******************************************************************//
    //**************************MAIN PAGE FILTER************************//
    //******************************************************************//
    #[Route('/api/main_filter', name: 'api_main_filter')]
    public function ajaxDateFilter(Request $request, EventRepository $eventRepository, CampusRepository $campusRepository, StatusRepository $statusRepository, SubscriptionRepository $subscriptionRepository, $userSubs = []): Response
    {
        $date = new DateTime();
        $date->getTimestamp();
        $date->sub(new DateInterval('P1M'));

        $data = $request->toArray();
        $campus = $data['campus'];
        $event = $data['text'];
        $dateFrom = $data['dateFrom'];
        $dateTo = $data['dateTo'];
        $user = $data['user'];
        $userSub = $data['userSub'];
        $userNonsub = $data['userNonsub'];
        $status = $data['past'];

        $campus = $campusRepository->findBy(['name' => $campus]);
        if ($user) {
            $user = $this->getUser();
        }
        if ($status) {
            $status = $statusRepository->findBy(['state' => 'Finished']);
        }
        if ($userSub || $userNonsub) {
            $userName = $this->getUser();
            $userSubList = $subscriptionRepository->findBy(['user' => $userName]);
            foreach ($userSubList as $sub) {
                $sub = $sub->getEvent()->getName();
                $userSubs [] = $sub;
            }
        }
        $listEvents = $eventRepository->mainSearch($date, $campus, $event, $dateFrom, $dateTo, $user, $userSub, $userNonsub, $userSubs, $status);
        return new Response($this->renderView('templates/_main_table.html.twig', [
            'eventList' => $listEvents,
        ]));
    }

    //******************************************************************//
    //*************************USER SUBSCRIPTION************************//
    //******************************************************************//
    #[Route('/api/user_sub', name: 'api_user_sub')]
    public function ajaxUserSub(Request $request, UserRepository $userRepository, EventRepository $eventRepository, SubscriptionRepository $subscriptionRepository): Response
    {
        $data = $request->toArray();
        $user = $data['user'];
        $event = $data['event'];
        $user = $userRepository->findOneBy(['name' => $user]);
        $event = $eventRepository->findOneBy(['name' => $event]);
        $subscription = $subscriptionRepository->findOneBy(['user' => $user, 'event' => $event]);

        if (is_null($subscription)) {
            $subscription = new Subscription();
            $date = new DateTime();
            $date->getTimestamp();
            $subscription->setDate($date);
            $subscription->setUser($user);
            $subscription->setEvent($event);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($subscription);
            $entityManager->flush();
        } else {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($subscription);
            $entityManager->flush();
        }

        $listEvents = $eventRepository->findAll();
        return new Response($this->renderView('templates/_main_table.html.twig', [
            'eventList' => $listEvents,
        ]));
    }
}