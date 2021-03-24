<?php

namespace App\Controller;

use App\Classes\Filters;
use App\Repository\CampusRepository;
use App\Repository\EventRepository;
use App\Service\ProcessFiltersInterface;
use App\Service\ProcessHomeInterface;
use App\Service\ProcessSubInterface;
use Mobile_Detect;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class HomeController extends AbstractController
{
    private ProcessHomeInterface $processHome;
    private ProcessFiltersInterface $processFilters;
    private EventRepository $eventRepository;
    private CampusRepository $campusRepository;
    private ProcessSubInterface $processSub;
    private SerializerInterface $serializer;

    public function __construct(SerializerInterface $serializer, ProcessHomeInterface $processHome,
                                ProcessFiltersInterface $processFilters, EventRepository $eventRepository,
                                CampusRepository $campusRepository, ProcessSubInterface $processSub)
    {
        $this->processHome = $processHome;
        $this->processFilters = $processFilters;
        $this->eventRepository = $eventRepository;
        $this->campusRepository = $campusRepository;
        $this->processSub = $processSub;
        $this->serializer = $serializer;
    }

    //**************************MAIN PAGE LOAD**************************//
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        $detect = new Mobile_Detect;
        if ($detect->isMobile() && !$detect->isTablet()) {
            $listEvents = $this->processHome->processMobileHome($this->processFilters, $this->eventRepository);
            return $this->render('home/home_mobile.html.twig', [
                'eventList' => $listEvents,
            ]);
        }
        $listEvents = $this->processHome->processHome($this->processFilters, $this->eventRepository);
        $campusList = $this->campusRepository->findAll();
        return $this->render('home/index.html.twig', [
            'eventList' => $listEvents,
            'campusList' => $campusList,
        ]);
    }

    //**************************MAIN PAGE FILTER************************//
    #[Route('/api/main_filter', name: 'api_main_filter')]
    public function ajaxDateFilter(Request $request): Response
    {
        $data = $request->getContent();
        $filters = $this->serializer->deserialize($data, Filters::class, 'json', ['groups' => 'filters']);
        $filters = $this->processFilters->processFilters($filters, $userSubs = []);
        $date = $this->processFilters->archiveDate();
        $listEvents = $this->eventRepository->mainSearch($date, $filters);
        return new Response($this->renderView('templates/_main_table.html.twig', [
            'eventList' => $listEvents,
        ]));
    }

    //*************************USER SUBSCRIPTION************************//
    #[Route('/api/user_sub', name: 'api_user_sub')]
    public function ajaxUserSub(Request $request): Response
    {
        $data = $request->toArray();
        $date = $this->processFilters->archiveDate();
        $this->processSub->processSub($data);
        $listEvents = $this->eventRepository->filterArchive($date);
        return new Response($this->renderView('templates/_main_table.html.twig', [
            'eventList' => $listEvents,
        ]));
    }
}