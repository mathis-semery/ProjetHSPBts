<?php

namespace App\Controller;

use App\Entity\Evenement;
use App\Entity\Inscription;
use App\Form\EvenementType;
use App\Repository\EvenementRepository;
use App\Repository\InscriptionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/evenement')]
final class EvenementController extends AbstractController
{
    #[Route(name: 'app_evenement_index', methods: ['GET'])]
    public function index(EvenementRepository $evenementRepository): Response
    {
        return $this->render('evenement/index.html.twig', [
            'evenements' => $evenementRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_evenement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $evenement = new Evenement();
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($evenement);
            $entityManager->flush();

            return $this->redirectToRoute('app_evenement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('evenement/new.html.twig', [
            'evenement' => $evenement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_evenement_show', methods: ['GET'])]
    public function show(Evenement $evenement): Response
    {
        return $this->render('evenement/show.html.twig', [
            'evenement' => $evenement,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_evenement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Evenement $evenement, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_evenement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('evenement/edit.html.twig', [
            'evenement' => $evenement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_evenement_delete', methods: ['POST'])]
    public function delete(Request $request, Evenement $evenement, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$evenement->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($evenement);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_evenement_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * Inscription à un événement
     */
    #[Route('/{id}/register', name: 'app_evenement_register', methods: ['POST'])]
    public function register(
        Evenement $evenement,
        EntityManagerInterface $entityManager,
        InscriptionRepository $inscriptionRepository
    ): Response {
        // Vérifier que l'utilisateur est connecté
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('danger', 'Vous devez être connecté pour vous inscrire à un événement.');
            return $this->redirectToRoute('app_login');
        }

        // Vérifier que l'utilisateur n'est pas déjà inscrit
        $inscriptionExistante = $inscriptionRepository->findByUserAndEvenement($user, $evenement);
        if ($inscriptionExistante !== null) {
            $this->addFlash('warning', 'Vous êtes déjà inscrit à cet événement.');
            return $this->redirectToRoute('app_evenement_show', ['id' => $evenement->getId()]);
        }

        // Vérifier qu'il y a des places disponibles
        if (!$evenement->hasPlacesDisponibles()) {
            $this->addFlash('danger', 'Il n\'y a plus de places disponibles pour cet événement.');
            return $this->redirectToRoute('app_evenement_show', ['id' => $evenement->getId()]);
        }

        // Créer l'inscription
        $inscription = new Inscription();
        $inscription->setRefUser($user);
        $inscription->setRefEvenement($evenement);
        $inscription->setEtat(true);
        $inscription->setDate(new \DateTime());

        $entityManager->persist($inscription);
        $entityManager->flush();

        $this->addFlash('success', 'Vous êtes inscrit avec succès à cet événement.');

        return $this->redirectToRoute('app_evenement_show', ['id' => $evenement->getId()]);
    }

    /**
     * Désinscription d'un événement
     */
    #[Route('/{id}/unregister', name: 'app_evenement_unregister', methods: ['POST'])]
    public function unregister(
        Evenement $evenement,
        EntityManagerInterface $entityManager,
        InscriptionRepository $inscriptionRepository
    ): Response {
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('danger', 'Vous devez être connecté.');
            return $this->redirectToRoute('app_login');
        }

        $inscription = $inscriptionRepository->findByUserAndEvenement($user, $evenement);
        if (!$inscription) {
            $this->addFlash('warning', 'Vous n\'êtes pas inscrit à cet événement.');
            return $this->redirectToRoute('app_evenement_show', ['id' => $evenement->getId()]);
        }

        $entityManager->remove($inscription);
        $entityManager->flush();

        $this->addFlash('success', 'Vous avez été désinscrit de cet événement.');

        return $this->redirectToRoute('app_evenement_show', ['id' => $evenement->getId()]);
    }
}
