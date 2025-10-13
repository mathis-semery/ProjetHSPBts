<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class AttenteVerificationController extends AbstractController
{
    #[Route('/attente-verification', name: 'app_attente_verification')]
    public function __invoke(): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // Dans ton projet, la date de création est stockée dans getDateCreation(): ?\DateTime
        $createdAt = method_exists($user, 'getDateCreation') ? $user->getDateCreation() : null;

        return $this->render('security/attente_verification.html.twig', [
            'createdAt' => $createdAt,
            'since'     => $this->since($createdAt),
        ]);
    }

    private function since(?\DateTimeInterface $date): string
    {
        if (!$date) return 'inconnu';
        $i = $date->diff(new \DateTimeImmutable());
        $parts = [];
        if ($i->y) $parts[] = $i->y.' an'.($i->y > 1 ? 's' : '');
        if ($i->m) $parts[] = $i->m.' mois';
        if ($i->d && \count($parts) < 2) $parts[] = $i->d.' j';
        if ($i->h && \count($parts) < 3) $parts[] = $i->h.' h';
        if ($i->i && \count($parts) < 3) $parts[] = $i->i.' min';
        return $parts ? implode(' ', $parts) : 'moins d’une minute';
    }
}
