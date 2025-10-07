<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

final class AttenteController extends AbstractController
{
    #[Route('/attente', name: 'app_attente')]
    public function __invoke(): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $createdAt = method_exists($user, 'getCreatedAt') ? $user->getCreatedAt() : null;

        return $this->render('security/attente.html.twig', [
            'user'      => $user,
            'createdAt' => $createdAt,
            'since'     => $this->since($createdAt),
            'hasRoleAttente' => $this->isGranted('ROLE_ATTENTE'),
            'hasRoleAttenteVerif' => $this->isGranted('ROLE_ATTENTE_VERIFICATION'),
        ]);
    }

    private function since(?\DateTimeInterface $date): string
    {
        if (!$date) return 'inconnu';
        $i = $date->diff(new \DateTimeImmutable());
        $p = [];
        if ($i->y) $p[] = $i->y.' an'.($i->y>1?'s':'');
        if ($i->m) $p[] = $i->m.' mois';
        if ($i->d && \count($p) < 2) $p[] = $i->d.' j';
        if ($i->h && \count($p) < 3) $p[] = $i->h.' h';
        if ($i->i && \count($p) < 3) $p[] = $i->i.' min';
        return $p ? implode(' ', $p) : 'moins d’une minute';
    }
}
