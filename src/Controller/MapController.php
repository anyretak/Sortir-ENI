<?php

namespace App\Controller;

use App\Repository\CampusRepository;
use App\Service\ProcessFilters;
use App\Service\ProcessMap;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class MapController extends AbstractController
{
    #[Route ('/map', name: 'map')]
    public function map(CampusRepository $campusRepository) {
        $campusList = $campusRepository->findAll();
        return $this->render('map/map.html.twig', [
            'campusList'=>$campusList,
        ]);
    }

    #[Route ('api/map', name: 'api_map')]
    public function ajaxMap(Request $request, ProcessMap $processMap, ProcessFilters $processFilters, SerializerInterface $serializer) {
        $data = $request->toArray();
        $eventCoords = $processMap->processMap($data, $processFilters);
        $eventsJson = $serializer->serialize($eventCoords, 'json');
        return new JsonResponse($eventsJson);
    }
}