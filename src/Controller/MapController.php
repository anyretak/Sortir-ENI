<?php

namespace App\Controller;

use App\Repository\CampusRepository;
use App\Service\ProcessFiltersInterface;
use App\Service\ProcessMapInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class MapController extends AbstractController
{
    private ProcessFiltersInterface $processFilters;
    private SerializerInterface $serializer;
    private ProcessMapInterface $processMap;
    private CampusRepository $campusRepository;

    public function __construct(ProcessFiltersInterface $processFilters, SerializerInterface $serializer,
                                ProcessMapInterface $processMap, CampusRepository $campusRepository)
    {
        $this->processFilters = $processFilters;
        $this->serializer = $serializer;
        $this->processMap = $processMap;
        $this->campusRepository = $campusRepository;
    }
    #[Route ('/map', name: 'map')]
    public function map()
    {
        $campusList = $this->campusRepository->findAll();
        return $this->render('map/map.html.twig', [
            'campusList' => $campusList,
        ]);
    }

    #[Route ('api/map', name: 'api_map')]
    public function ajaxMap(Request $request)
    {
        $data = $request->toArray();
        $eventCoords = $this->processMap->processMap($data, $this->processFilters);
        $eventsJson = $this->serializer->serialize($eventCoords, 'json');
        return new JsonResponse($eventsJson);
    }
}