<?php

namespace App\Controller;

use App\Entity\Candidature;
use App\Form\CandidatureType;
use App\Repository\CandidatureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/candidature')]
final class CandidatureController extends AbstractController
{
    #[Route(name: 'app_candidature_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(CandidatureRepository $candidatureRepository): Response
    {
        return $this->render('candidature/index.html.twig', [
            'candidatures' => $candidatureRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_candidature_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $candidature = new Candidature();
        $form = $this->createForm(CandidatureType::class, $candidature);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($candidature);
            $entityManager->flush();

            return $this->redirectToRoute('app_candidature_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('candidature/new.html.twig', [
            'candidature' => $candidature,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_candidature_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(Candidature $candidature): Response
    {
        return $this->render('candidature/show.html.twig', [
            'candidature' => $candidature,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_candidature_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, Candidature $candidature, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CandidatureType::class, $candidature);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_candidature_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('candidature/edit.html.twig', [
            'candidature' => $candidature,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_candidature_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, Candidature $candidature, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$candidature->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($candidature);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_candidature_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/accepter', name: 'app_candidature_accepter', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function accepter(Candidature $candidature, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $offre = $candidature->getRefOffre();

        // Vérifier les droits : admin OU même entreprise que l'auteur de l'offre
        if (!$this->isGranted('ROLE_ADMIN')) {
            if (!$user->getrefEntreprise() || !$offre->getAuteur()->getrefEntreprise()) {
                throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette fonctionnalité.');
            }

            if ($user->getrefEntreprise()->getId() !== $offre->getAuteur()->getrefEntreprise()->getId()) {
                throw $this->createAccessDeniedException('Vous ne pouvez pas accepter cette candidature.');
            }
        }

        // Mettre à jour l'état de la candidature
        $candidature->setEtat(true);

        // Fermer l'offre : passer l'état de 1 (true) à 0 (false)
        $offre->setEtat(false);

        $entityManager->flush();

        $this->addFlash('success', 'La candidature a été acceptée avec succès et l\'offre a été fermée au public.');

        return $this->redirectToRoute('app_offre_show', ['id' => $offre->getId()]);
    }

    #[Route('/{id}/refuser', name: 'app_candidature_refuser', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function refuser(Candidature $candidature, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $offre = $candidature->getRefOffre();

        // Vérifier les droits : admin OU même entreprise que l'auteur de l'offre
        if (!$this->isGranted('ROLE_ADMIN')) {
            if (!$user->getrefEntreprise() || !$offre->getAuteur()->getrefEntreprise()) {
                throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette fonctionnalité.');
            }

            if ($user->getrefEntreprise()->getId() !== $offre->getAuteur()->getrefEntreprise()->getId()) {
                throw $this->createAccessDeniedException('Vous ne pouvez pas refuser cette candidature.');
            }
        }

        // Mettre à jour l'état de la candidature
        $candidature->setEtat(false);
        $entityManager->flush();

        $this->addFlash('success', 'La candidature a été refusée.');

        return $this->redirectToRoute('app_offre_show', ['id' => $offre->getId()]);
    }
}
