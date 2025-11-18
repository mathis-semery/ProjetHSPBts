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
            if (in_array('ROLE_ADMIN', $user->getRoles())) {
                return true; // admin voit tout
            }
            $metiers = array_map('trim', explode(',', $canal->getListeAuto() ?? ''));
            return in_array($user->getMetier(), $metiers) || $canal->getRefUser() === $user;
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
            $em->persist($canal);
            $em->flush();
            $this->addFlash('success', 'Canal créé !');

            return $this->redirectToRoute('app_canal_index');
        }

        return $this->render('canal/new.html.twig', [
            'canal' => $canal,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/view', name: 'app_canal_view', methods: ['GET','POST'])]
    public function view(Request $request, Canal $canal, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        if (!in_array('ROLE_ADMIN', $user->getRoles())) {
            $metiers = array_map('trim', explode(',', $canal->getListeAuto() ?? ''));
            if (!in_array($user->getMetier(), $metiers) && $canal->getRefUser() !== $user) {
                throw $this->createAccessDeniedException('Vous ne pouvez pas accéder à ce canal.');
            }
        }

        $post = new \App\Entity\Post();
        $formPost = $this->createForm(\App\Form\PostType::class, $post);
        $formPost->handleRequest($request);

        if ($formPost->isSubmitted() && $formPost->isValid()) {
            $post->setRefUser($user);
            $post->setRefCanal($canal);

            $em->persist($post);
            $em->flush();

            return $this->redirectToRoute('app_canal_view', ['id' => $canal->getId()]);
        }

        if ($request->isMethod('POST') && $request->request->has('texte') && $request->request->has('post_id')) {
            $texte = trim($request->request->get('texte'));
            $postId = $request->request->getInt('post_id');
            $parentPost = $em->getRepository(\App\Entity\Post::class)->find($postId);

            if ($texte !== '' && $parentPost) {
                $reponse = new \App\Entity\Reponse();
                $reponse->setRefUser($user);
                $reponse->setRefPost($parentPost);
                $reponse->setTexte($texte);
                $reponse->setDateHeure(new \DateTime());

                $em->persist($reponse);
                $em->flush();

                return $this->redirectToRoute('app_canal_view', ['id' => $canal->getId()]);
            }
        }

        return $this->render('canal/view.html.twig', [
            'canal' => $canal,
            'formPost' => $formPost->createView(),
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

    #[Route('/post/{id}/delete', name: 'app_post_delete', methods: ['POST'])]
    public function deletePost(Request $request, Post $post, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException();
        }

        // Autoriser seulement l'auteur ou l'admin
        if ($post->getRefUser() === $user || in_array('ROLE_ADMIN', $user->getRoles())) {
            if ($this->isCsrfTokenValid('delete'.$post->getId(), $request->request->get('_token'))) {
                $em->remove($post);
                $em->flush();
            }
        }

        return $this->redirectToRoute('app_canal_view', ['id' => $post->getCanal()->getId()]);
    }

    #[Route('/reponse/{id}/delete', name: 'app_reponse_delete', methods: ['POST'])]
    public function deleteReponse(Request $request, Reponse $reponse, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException();
        }

        if ($reponse->getRefUser() === $user || in_array('ROLE_ADMIN', $user->getRoles())) {
            if ($this->isCsrfTokenValid('delete'.$reponse->getId(), $request->request->get('_token'))) {
                $em->remove($reponse);
                $em->flush();
            }
        }

        return $this->redirectToRoute('app_canal_view', ['id' => $reponse->getPost()->getCanal()->getId()]);
    }
}
