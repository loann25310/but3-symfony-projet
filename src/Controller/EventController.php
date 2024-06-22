<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\User;
use App\Form\EventType;
use App\Form\RegisterEventFormType;
use App\Repository\EventRepository;
use App\Service\NotificationManagerInterface;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/event')]
class EventController extends AbstractController
{

    private NotificationManagerInterface $notificationManager;

    public function __construct(NotificationManagerInterface $notificationManager)
    {
        $this->notificationManager = $notificationManager;
    }

    #[Route('/', name: 'app_event_index', methods: ['GET'])]
    public function index(Request $request, EventRepository $eventRepository): Response
    {
        $page = $request->query->getInt('page', 1);

        $c = Criteria::create()
            ->orderBy(['date' => Order::Ascending])
            ->setFirstResult(($page - 1) * 10)
            ->setMaxResults(10);

        return $this->render('pages/event/index.html.twig', [
            'events' => $eventRepository->matching($c),
            'page' => $page,
            'pages' => ceil($eventRepository->count([]) / 10),
        ]);
    }

    #[Route('/new', name: 'app_event_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($event);
            $entityManager->flush();

            return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('pages/event/new.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }


    #[Route('/registereds', name: 'app_event_registereds', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function registereds(EventRepository $eventRepository): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->render('pages/event/registereds.html.twig', [
            'events' => $eventRepository->findByParticipant($user),
        ]);
    }

    #[Route('/{id}', name: 'app_event_show', methods: ['GET'])]
    public function show(Event $event): Response
    {
        return $this->render('pages/event/show.html.twig', [
            'event' => $event,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_event_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('pages/event/edit.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/cancelRegistration', name: 'app_event_cancel_registration', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function cancelRegistration(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        $event->removeParticipant($user);
        $entityManager->flush();

        $this->addFlash('success', 'Votre inscription a bien été annulée');
        $this->notificationManager->sendRegistrationCancellation($user, $event);

        return $this->redirectToRoute('app_event_registereds', ['id' => $event->getId()], Response::HTTP_SEE_OTHER);
    }


    #[Route('/{id}', name: 'app_event_delete', methods: ['POST'])]
    public function delete(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$event->getId(), $request->getPayload()->get('_token'))) {

            /** @var User $user */
            $user = $this->getUser();

            // Ensure that is the owner of the event
            if ($event->getCreatedBy()->getId() !== $user->getId()) {
                throw $this->createAccessDeniedException();
            }

            $entityManager->remove($event);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/register', name: 'app_event_register', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function register(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(RegisterEventFormType::class, [
            'event' => $event,
            'user' => $user,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $event->addParticipant($user);
                $entityManager->flush();

                $this->addFlash('success', 'Vous êtes bien inscrit à l\'événement');

                $this->notificationManager->sendRegistrationConfirmation($user, $event);

            } catch (\Exception $e) {
                dd($e);
                $this->addFlash('error', 'Votre inscription n\'a pas pu être prise en compte, l\'événement est complet');
            }
            return $this->redirectToRoute('app_event_show', ['id' => $event->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('pages/event/register.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }

}
