<?php

namespace App\Controller;

use App\Entity\Candidature;
use App\Entity\Offre;
use App\Form\OffreType;
use App\Repository\OffreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/offre')]
final class OffreController extends AbstractController
{
    #[Route(name: 'app_offre_index', methods: ['GET'])]
    public function index(Request $request, OffreRepository $offreRepository): Response
    {
        $search = $request->query->get('search', '');
        $type = $request->query->get('type', '');
        $mesOffres = $request->query->get('mes_offres', '0');

        $queryBuilder = $offreRepository->createQueryBuilder('o')
            ->orderBy('o.id', 'DESC');

        // Filtrer par offres de mon entreprise
        if ($mesOffres === '1' && $this->getUser()) {
            $user = $this->getUser();
            $entreprise = $user->getrefEntreprise();

            if ($entreprise) {
                $queryBuilder->join('o.auteur', 'u')
                    ->andWhere('u.refEntreprise = :entreprise')
                    ->setParameter('entreprise', $entreprise);
            } else {
                // Si pas d'entreprise, ne montrer aucune offre
                $queryBuilder->andWhere('1 = 0');
            }
        }

        // Filtrer par recherche (titre ou description)
        if (!empty($search)) {
            $queryBuilder->andWhere('o.titre LIKE :search OR o.description LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        // Filtrer par type
        if (!empty($type)) {
            $queryBuilder->andWhere('o.type = :type')
                ->setParameter('type', $type);
        }

        $offres = $queryBuilder->getQuery()->getResult();

        return $this->render('offre/index.html.twig', [
            'offres' => $offres,
            'search' => $search,
            'type' => $type,
            'mes_offres' => $mesOffres,
        ]);
    }

    #[Route('/new', name: 'app_offre_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Vérifier que l'utilisateur est partenaire ou admin
        if (!$this->isGranted('ROLE_PARTENAIRE') && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Vous devez être partenaire ou administrateur pour créer une offre.');
        }
        $offre = new Offre();
        $form = $this->createForm(OffreType::class, $offre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Définir l'auteur de l'offre
            $offre->setAuteur($this->getUser());

            $entityManager->persist($offre);
            $entityManager->flush();

            $this->addFlash('success', 'L\'offre a été créée avec succès.');
            return $this->redirectToRoute('app_offre_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('offre/new.html.twig', [
            'offre' => $offre,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_offre_show', methods: ['GET'])]
    public function show(Offre $offre, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $dejaCandidature = false;

        // Vérifier si l'utilisateur a déjà postulé
        if ($user) {
            $candidatureExistante = $entityManager->getRepository(Candidature::class)
                ->findOneBy(['refUser' => $user, 'refOffre' => $offre]);

            $dejaCandidature = $candidatureExistante !== null;
        }

        return $this->render('offre/show.html.twig', [
            'offre' => $offre,
            'dejaCandidature' => $dejaCandidature,
        ]);
    }

    #[Route('/{id}/postuler', name: 'app_offre_postuler', methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function postuler(Request $request, Offre $offre, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        // Vérifier que l'offre est ouverte
        if (!$offre->isEtat()) {
            $this->addFlash('error', 'Cette offre n\'est plus ouverte aux candidatures.');
            return $this->redirectToRoute('app_offre_show', ['id' => $offre->getId()]);
        }

        // Vérifier si l'utilisateur a déjà postulé
        $candidatureExistante = $entityManager->getRepository(Candidature::class)
            ->findOneBy(['refUser' => $user, 'refOffre' => $offre]);

        if ($candidatureExistante) {
            $this->addFlash('warning', 'Vous avez déjà postulé à cette offre.');
            return $this->redirectToRoute('app_offre_show', ['id' => $offre->getId()]);
        }

        // Récupérer la lettre de motivation
        $lettreMotivation = $request->request->get('lettre_motivation');

        if (empty($lettreMotivation)) {
            $this->addFlash('error', 'Veuillez saisir une lettre de motivation.');
            return $this->redirectToRoute('app_offre_show', ['id' => $offre->getId()]);
        }

        // Créer la candidature
        $candidature = new Candidature();
        $candidature->setRefUser($user);
        $candidature->setRefOffre($offre);
        $candidature->setLettre($lettreMotivation);
        $candidature->setEtat(true); // État actif

        $entityManager->persist($candidature);
        $entityManager->flush();

        $this->addFlash('success', 'Votre candidature a été envoyée avec succès.');

        // TODO: Envoyer un email à l'auteur de l'offre

        return $this->redirectToRoute('app_offre_show', ['id' => $offre->getId()]);
    }

    #[Route('/{id}/edit', name: 'app_offre_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Offre $offre, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        // Vérifier que l'utilisateur est partenaire ou admin
        if (!$this->isGranted('ROLE_PARTENAIRE') && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Vous devez être partenaire ou administrateur pour modifier une offre.');
        }

        // Admin a accès à tout
        if ($this->isGranted('ROLE_ADMIN')) {
            // Admin peut tout modifier
        }
        // Partenaire : vérifier qu'il est l'auteur OU fait partie de la même entreprise que l'auteur
        elseif ($this->isGranted('ROLE_PARTENAIRE')) {
            // Si l'utilisateur est l'auteur, il peut modifier
            if ($offre->getAuteur() === $user) {
                // OK, c'est son offre
            } else {
                // Sinon, vérifier qu'il fait partie de la même entreprise
                $entrepriseUser = $user->getrefEntreprise();
                $entrepriseAuteur = $offre->getAuteur()->getrefEntreprise();

                if ($entrepriseUser === null || $entrepriseAuteur === null || $entrepriseUser->getId() !== $entrepriseAuteur->getId()) {
                    throw $this->createAccessDeniedException('Vous ne pouvez pas modifier cette offre car elle n\'appartient pas à votre entreprise.');
                }
            }
        }

        $form = $this->createForm(OffreType::class, $offre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'L\'offre a été modifiée avec succès.');
            return $this->redirectToRoute('app_offre_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('offre/edit.html.twig', [
            'offre' => $offre,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_offre_delete', methods: ['POST'])]
    public function delete(Request $request, Offre $offre, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        // Vérifier que l'utilisateur est partenaire ou admin
        if (!$this->isGranted('ROLE_PARTENAIRE') && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Vous devez être partenaire ou administrateur pour supprimer une offre.');
        }

        // Admin a accès à tout
        if ($this->isGranted('ROLE_ADMIN')) {
            // Admin peut tout supprimer
        }
        // Partenaire : vérifier qu'il est l'auteur OU fait partie de la même entreprise que l'auteur
        elseif ($this->isGranted('ROLE_PARTENAIRE')) {
            // Si l'utilisateur est l'auteur, il peut supprimer
            if ($offre->getAuteur() === $user) {
                // OK, c'est son offre
            } else {
                // Sinon, vérifier qu'il fait partie de la même entreprise
                $entrepriseUser = $user->getrefEntreprise();
                $entrepriseAuteur = $offre->getAuteur()->getrefEntreprise();

                if ($entrepriseUser === null || $entrepriseAuteur === null || $entrepriseUser->getId() !== $entrepriseAuteur->getId()) {
                    throw $this->createAccessDeniedException('Vous ne pouvez pas supprimer cette offre car elle n\'appartient pas à votre entreprise.');
                }
            }
        }

        if ($this->isCsrfTokenValid('delete'.$offre->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($offre);
            $entityManager->flush();

            $this->addFlash('success', 'L\'offre a été supprimée avec succès.');
        }

        return $this->redirectToRoute('app_offre_index', [], Response::HTTP_SEE_OTHER);
    }
}
