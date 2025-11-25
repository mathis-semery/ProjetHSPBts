<?php

namespace App\Controller;

use App\Entity\Canal;
use App\Form\CanalType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/canal')]
class CanalController extends AbstractController
{
    #[Route('/', name: 'app_canal_index', methods: ['GET'])]
    public function index(EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $canals = $em->getRepository(Canal::class)->findAll();

        $visibleCanals = array_filter($canals, function(Canal $canal) use ($user) {
            // Les admins voient tout
            if (in_array('ROLE_ADMIN', $user->getRoles())) {
                return true;
            }

            $rolesAutorises = $canal->getListeAuto() ?? []; // tableau de rôles
            return in_array($user->getMetier(), $rolesAutorises) || $canal->getRefUser() === $user;
        });

        return $this->render('canal/index.html.twig', [
            'canals' => $visibleCanals,
        ]);
    }

    #[Route('/new', name: 'app_canal_new', methods: ['GET','POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $canal = new Canal();
        $canal->setRefUser($this->getUser());

        $form = $this->createForm(CanalType::class, $canal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Si ListeAuto est en string, transformer en CSV
            // $canal->setListeAuto(implode(',', $form->get('ListeAuto')->getData()));

            $em->persist($canal);
            $em->flush();
            $this->addFlash('success', 'Canal créé !');

            return $this->redirectToRoute('app_canal_index');
        }

        return $this->render('canal/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_canal_edit', methods: ['GET','POST'])]
    public function edit(Request $request, Canal $canal, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        if (!in_array('ROLE_ADMIN', $user->getRoles()) && $canal->getRefUser() !== $user) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas modifier ce canal.');
        }

        $form = $this->createForm(CanalType::class, $canal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Canal modifié !');

            return $this->redirectToRoute('app_canal_index');
        }

        return $this->render('canal/edit.html.twig', [
            'canal' => $canal,
            'form' => $form->createView(),
        ]);
    }
}
