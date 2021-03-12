<?php

namespace App\Controller;

use App\Form\LocationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LocationController extends AbstractController
{
    #[Route('/add_location', name: 'add_location')]
    public function index(Request $request): Response
    {
        $form = $this->createForm(LocationType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $location = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($location);
            $entityManager->flush();

            $this->addFlash(
                'notice',
                'Success! New location was added.'
            );

            return $this->redirectToRoute('event');
        }

        return $this->render('location/add_location.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
