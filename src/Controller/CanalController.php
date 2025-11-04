<?php

namespace App\Controller;

use App\Entity\Canal;
use App\Entity\Post;
use App\Entity\Reponse;
use App\Form\CanalType;
use App\Form\PostType;
use App\Repository\UserRepository;
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

        $visibleCanals = array_filter($canals, fn($c) => in_array($user->getMetier(), array_map('trim', explode(',', $c->getListeAuto() ?? ''))) || $c->getRefUser() === $user);

        return $this->render('canal/index.html.twig', [
            'canals' => $visibleCanals,
        ]);
    }

    #[Route('/new', name: 'app_canal_new', methods: ['GET','POST'])]
    public function new(Request $request, EntityManagerInterface $em, UserRepository $userRepo): Response    {
        $canal = new Canal();
        $form = $this->createForm(CanalType::class, $canal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $canal->setRefUser($this->getUser());
            $em->persist($canal);
            $em->flush();

            $this->applyListeAuto($canal, $userRepo);

            $em->flush();
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
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $post = new Post();
        $formPost = $this->createForm(PostType::class, $post);
        $formPost->handleRequest($request);

        if ($formPost->isSubmitted() && $formPost->isValid()) {
            $post->setRefUser($this->getUser());
            $post->setRefCanal($canal);
            $em->persist($post);
            $em->flush();
            return $this->redirectToRoute('app_canal_view', ['id' => $canal->getId()]);
        }

        if ($request->isMethod('POST') && $request->request->has('texte') && $request->request->has('post_id')) {
            $texte = trim($request->request->get('texte'));
            $postId = $request->request->getInt('post_id');
            $parentPost = $em->getRepository(Post::class)->find($postId);

            if ($texte !== '' && $parentPost) {
                $reponse = new Reponse();
                $reponse->setRefUser($this->getUser());
                $reponse->setRefPost($parentPost);
                $reponse->setTexte($texte);
                $reponse->setDateHeure(new \DateTime());

                if ($request->request->has('parent_id')) {
                    $parentId = $request->request->getInt('parent_id');
                    $parentReponse = $em->getRepository(Reponse::class)->find($parentId);
                    if ($parentReponse) {
                        $reponse->setParent($parentReponse);
                    }
                }

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
    public function edit(Request $request, Canal $canal, EntityManagerInterface $em, UserRepository $userRepo): Response
    {
        $form = $this->createForm(CanalType::class, $canal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $canal->setRefUser($this->getUser());
            $em->persist($canal);
            $em->flush();

            $this->applyListeAuto($canal, $userRepo);

            $em->flush();
            return $this->redirectToRoute('app_canal_index');
        }

        return $this->render('canal/edit.html.twig', [
            'canal' => $canal,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'app_canal_delete', methods: ['POST'])]
    public function delete(Request $request, Canal $canal, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$canal->getId(), $request->request->get('_token'))) {
            $em->remove($canal);
            $em->flush();
        }

        return $this->redirectToRoute('app_canal_index');
    }
    private function applyListeAuto(Canal $canal, UserRepository $userRepo): void
    {
        $listeAuto = $canal->getListeAuto();
        if (!$listeAuto) return;


        $canal->getUsers()->clear();

        $items = array_map('trim', explode(',', $listeAuto));

        foreach ($items as $item) {
            $users = $userRepo->findBy(['metier' => $item]);

            foreach ($users as $user) {
                $canal->addUser($user);
            }
        }
    }

}
