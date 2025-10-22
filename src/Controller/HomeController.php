<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/')]
final class HomeController extends AbstractController
{
    #[Route(name: 'app_home', methods: ['GET'])]
    public function index(): Response
    {

        return $this->render('home/home.html.twig');
    }
}
