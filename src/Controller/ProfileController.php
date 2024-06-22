<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfileEditType;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class ProfileController extends AbstractController
{
    #[Route('/profile/edit', name: 'app_profile_edit')]
    public function edit(Request $request, EntityManagerInterface $entityManager, UserInterface $user): Response
    {
        // Récupérer l'utilisateur connecté
        // $user = $this->getUser();

        // Créer le formulaire et le lier à l'entité User
        $form = $this->createForm(RegistrationFormType::class, $user);

        // Traiter la requête
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Enregistrer les modifications
            $entityManager->persist($user);
            $entityManager->flush();

            // Ajouter un message de succès et rediriger l'utilisateur
            $this->addFlash('success', 'Votre profil a été mis à jour avec succès.');
            return $this->redirectToRoute('home');
        }

        return $this->render('pages/user/edit.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}