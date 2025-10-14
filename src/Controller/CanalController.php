<?php

namespace App\Controller;

use App\Entity\Canal;
use App\Form\CanalType;
use App\Repository\CanalRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/canal')]
final class CanalController extends AbstractController
{
    #[Route(name: 'app_canal_index', methods: ['GET'])]
    public function index(CanalRepository $canalRepository): Response
    {
        return $this->render('canal/index.html.twig', [
            'canals' => $canalRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_canal_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $canal = new Canal();
        $form = $this->createForm(CanalType::class, $canal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($canal);
            $entityManager->flush();

            return $this->redirectToRoute('app_canal_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('canal/new.html.twig', [
            'canal' => $canal,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_canal_show', methods: ['GET'])]
    public function show(Canal $canal): Response
    {
        return $this->render('canal/show.html.twig', [
            'canal' => $canal,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_canal_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Canal $canal, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CanalType::class, $canal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_canal_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('canal/edit.html.twig', [
            'canal' => $canal,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_canal_delete', methods: ['POST'])]
    public function delete(Request $request, Canal $canal, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$canal->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($canal);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_canal_index', [], Response::HTTP_SEE_OTHER);
    }
}
