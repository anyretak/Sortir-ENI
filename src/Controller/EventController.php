<?php

namespace App\Controller;

use App\Form\NewEventType;
use App\Repository\EventRepository;
use App\Repository\LocationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EventController extends AbstractController
{
    #[Route('/new_event', name: 'event')]
    public function newEvent(Request $request): Response
    {
        $form = $this->createForm(NewEventType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $task = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($task);
            $entityManager->flush();

            $this->addFlash(
                'notice',
                'Success! New event wad added.'
            );

            return $this->redirectToRoute('home');
        }


        return $this->render('event/new_event.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/event_details/{event}', name: 'event_details')]
    public function eventDetails($event, EventRepository $eventRepository) {

        $eventDetails = $eventRepository->findOneBy(['name'=>$event]);
        $locationDetails = $eventDetails->getLocation();
        $subscriptionDetails = $eventDetails->getSubscriptions();

        foreach ($subscriptionDetails as $detail) {
            $user = $detail->getUser();
            $userDetails[] = $user;
        }

        return $this->render('event/event_details.html.twig', [
            'eventDetails' => $eventDetails,
            'locationDetails'=> $locationDetails,
            'userDetails'=>$userDetails,
        ]);
    }
}
