<?php

namespace App\Controller;

use App\Form\CancelEventType;
use App\Form\NewEventType;
use App\Repository\EventRepository;
use App\Repository\StatusRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PropertyAccess\PropertyAccess;

class EventController extends AbstractController
{
    #[Route('/new_event', name: 'event')]
    public function newEvent(Request $request, StatusRepository $statusRepository): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(NewEventType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


/*            REMOVE DUPLICATED CODE*/
            if ($form->getClickedButton() === $form->get('create')) {

                $entityManager = $this->getDoctrine()->getManager();
                $event = $form->getData();
                $state = $statusRepository->findOneBy(['state' => 'Created']);
                $event->setStatus($state);
                $event->setUser($user);
                $entityManager->persist($event);
                $entityManager->flush();

                $this->addFlash(
                    'notice',
                    'Success! New event was created. Do you want to publish it now?'
                );

                return $this->redirectToRoute('event');
            }

            if ($form->getClickedButton() === $form->get('publish')) {

                $entityManager = $this->getDoctrine()->getManager();
                $event = $form->getData();
                $state = $statusRepository->findOneBy(['state' => 'Open']);
                $event->setStatus($state);
                $event->setUser($user);
                $entityManager->persist($event);
                $entityManager->flush();

                $this->addFlash(
                    'notice',
                    'Success! New event was created and it is now opened!'
                );

                return $this->redirectToRoute('home');
            }
        }

        return $this->render('event/new_event.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/event_details/{event}', name: 'event_details')]
    public function eventDetails($event, EventRepository $eventRepository, $userDetails = [])
    {
        $eventDetails = $eventRepository->findOneBy(['name' => $event]);
        $locationDetails = $eventDetails->getLocation();
        $subscriptionDetails = $eventDetails->getSubscriptions();

        foreach ($subscriptionDetails as $detail) {
            $user = $detail->getUser();
            $userDetails[] = $user;
        }

        return $this->render('event/event_details.html.twig', [
            'eventDetails' => $eventDetails,
            'locationDetails' => $locationDetails,
            'userDetails' => $userDetails,
        ]);
    }

    #[Route('/edit_event/{event}', name: 'edit_event')]
    public function editEvent(Request $request, $event, EventRepository $eventRepository, StatusRepository $statusRepository): Response
    {
        $event = $eventRepository->findOneBy(['name' => $event]);
        $location = $event->getLocation();
        $form = $this->createForm(NewEventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($form->getClickedButton() === $form->get('create')) {

                $entityManager = $this->getDoctrine()->getManager();
                $event = $form->getData();
                $state = $statusRepository->findOneBy(['state' => 'Created']);
                $event->setStatus($state);
                $entityManager->flush();

                $this->addFlash(
                    'notice',
                    'Success! Event details were updated.'
                );

                return $this->redirectToRoute('home');
            }

            if ($form->getClickedButton() === $form->get('publish')) {

                $entityManager = $this->getDoctrine()->getManager();
                $event = $form->getData();
                $state = $statusRepository->findOneBy(['state' => 'Open']);
                $event->setStatus($state);
                $entityManager->flush();

                $this->addFlash(
                    'notice',
                    'Success! Event is now open.'
                );

                return $this->redirectToRoute('home');
            }
        }

        return $this->render('event/edit_event.html.twig', [
            'form' => $form->createView(),
            'event' => $event,
        ]);
    }

    #[Route('/cancel_event/{event}', name: 'cancel_event')]
    public function cancelEvent(Request $request, $event, EventRepository $eventRepository, StatusRepository $statusRepository): Response
    {
        $event = $eventRepository->findOneBy(['name' => $event]);
        $location = $event->getLocation();

        $form = $this->createForm(CancelEventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $state = $statusRepository->findOneBy(['state' => 'Cancelled']);
            $event->setStatus($state);
            $entityManager->flush();

            $this->addFlash(
                'notice',
                'Success! Event details updated.'
            );

            return $this->redirectToRoute('home');
        }

        return $this->render('event/cancel_event.html.twig', [
            'form' => $form->createView(),
            'event' => $event,
            'location' => $location,
        ]);
    }


    #[Route('/ajax', name: 'ajax')]
    public function ajaxEvent(Request $request, EventRepository $eventRepository, StatusRepository $statusRepository): Response
    {
        if ($request->isXmlHttpRequest()) {

            $eventData = $request->request->all();
            $propertyAccessor = PropertyAccess::createPropertyAccessor();
            $event = $propertyAccessor->getValue($eventData, '[data]');

            $state = $statusRepository->findOneBy(['state' => 'Open']);
            $eventUpdated = $eventRepository->findOneBy(['name' => $event]);
            $eventUpdated->setStatus($state);
            $this->getDoctrine()->getManager()->flush();

            return new Response('Its coming from controller');
        }
    }
}
