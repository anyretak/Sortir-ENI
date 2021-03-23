<?php

namespace App\Controller;

use App\Classes\Filters;
use App\Service\ProcessFilters;
use App\Repository\CampusRepository;
use App\Repository\EventRepository;
use App\Service\ProcessHome;
use App\Service\ProcessSub;
use Mobile_Detect;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class HomeController extends AbstractController
{
    //**************************MAIN PAGE LOAD**************************//
    #[Route('/', name: 'home')]
    public function index(ProcessFilters $processFilters, ProcessHome $processHome, EventRepository $eventRepository, CampusRepository $campusRepository): Response
    {
        $detect = new Mobile_Detect;
        if ($detect->isMobile() && !$detect->isTablet()) {
            $listEvents = $processHome->processMobileHome($processFilters, $eventRepository);
            return $this->render('home/home_mobile.html.twig', [
                'eventList' => $listEvents,
            ]);
        }
        $listEvents = $processHome->processHome($processFilters, $eventRepository);
        $campusList = $campusRepository->findAll();
        return $this->render('home/index.html.twig', [
            'eventList' => $listEvents,
            'campusList' => $campusList,
        ]);
    }

    //**************************MAIN PAGE FILTER************************//
    #[Route('/api/main_filter', name: 'api_main_filter')]
    public function ajaxDateFilter(Request $request, SerializerInterface $serializer, EventRepository $eventRepository, ProcessFilters $processFilters): Response
    {
        $data = $request->getContent();
        $filters = $serializer->deserialize($data, Filters::class, 'json', ['groups' => 'filters']);
        $filters = $processFilters->processFilters($filters, $userSubs = []);
        $date = $processFilters->archiveDate();
        $listEvents = $eventRepository->mainSearch($date, $filters);
        return new Response($this->renderView('templates/_main_table.html.twig', [
            'eventList' => $listEvents,
        ]));
    }

    //*************************USER SUBSCRIPTION************************//
    #[Route('/api/user_sub', name: 'api_user_sub')]
    public function ajaxUserSub(Request $request, ProcessFilters $processFilters, ProcessSub $processSub, EventRepository $eventRepository): Response
    {
        $data = $request->toArray();
        $date = $processFilters->archiveDate();
        $processSub->processSub($data);
        $listEvents = $eventRepository->filterArchive($date);
        return new Response($this->renderView('templates/_main_table.html.twig', [
            'eventList' => $listEvents,
        ]));
    }
}