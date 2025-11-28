<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

#[Route('/admin/utilisateurs')]
class UserPendingController extends AbstractController
{
    #[Route('/en-attente', name: 'user_pending_index')]
    public function index(UserRepository $userRepository): Response
    {
        $users = $userRepository->findBy(['etat_validation' => null]);

        $users = array_unique($users, SORT_REGULAR);

        return $this->render('user_pending/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/{id}/accepter', name: 'user_pending_accept', methods: ['POST'])]
    public function accept(
        User $user,
        EntityManagerInterface $entityManager,
        MailerInterface $mailer

    ): Response {
        $user->setEtatValidation(true);

        // Récupérer les rôles actuels et retirer ROLE_ATTENTE_VERIFICATION
        $currentRoles = $user->getRolesForForm();
        $newRoles = array_filter($currentRoles, function($role) {
            return $role !== 'ROLE_ATTENTE_VERIFICATION';
        });

        // S'assurer qu'il y a au moins un rôle métier
        if (empty($newRoles)) {
            $newRoles = ['ROLE_USER'];
        }

        $user->setRoles(array_values($newRoles));
        $entityManager->flush();

        $email = (new Email())
            ->from('no-reply@' . $_ENV['NOM_DOMAINE'] ?? 'localhost')
            ->to($user->getEmail())
            ->subject('Votre inscription a été refusée')
            ->html(<<<HTML
<p>Bonjour {$user->getPrenom()},</p>
<p>Nous vous informons que votre demande d’inscription a été validée.</p>
<p>Pour toute question, veuillez contacter l’administrateur du site.</p>
<p>Cordialement,<br>L’équipe HSP</p>
HTML
            );

        $mailer->send($email);

        $this->addFlash('success', 'L’utilisateur a été accepté avec succès.');

        return $this->redirectToRoute('user_pending_index');
    }

    #[Route('/{id}/refuser', name: 'user_pending_refuse', methods: ['POST'])]
    public function refuse(
        User $user,
        EntityManagerInterface $entityManager,
        MailerInterface $mailer
    ): Response {
        $user->setEtatValidation(false);
        $entityManager->flush();

        $email = (new Email())
            ->from('no-reply@' . $_ENV['NOM_DOMAINE'] ?? 'localhost')
            ->to($user->getEmail())
            ->subject('Votre inscription a été refusée')
            ->html(<<<HTML
<p>Bonjour {$user->getPrenom()},</p>
<p>Nous vous informons que votre demande d’inscription n’a pas été validée.</p>
<p>Pour toute question, veuillez contacter l’administrateur du site.</p>
<p>Cordialement,<br>L’équipe HSP</p>
HTML
            );

        $mailer->send($email);

        $this->addFlash('warning', 'L’utilisateur a été refusé et un email de notification a été envoyé.');

        return $this->redirectToRoute('user_pending_index');
    }
}
