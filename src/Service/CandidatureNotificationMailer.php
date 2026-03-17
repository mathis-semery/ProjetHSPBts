<?php

namespace App\Service;

use App\Entity\Candidature;
use App\Entity\User;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class CandidatureNotificationMailer
{
    public function __construct(
        private MailerInterface $mailer,
        private string $fromAddress,
    ) {}

    /**
     * Envoie un email à tous les partenaires de l'entreprise liée à l'offre
     * pour les notifier d'une nouvelle candidature.
     */
    public function notifyPartenaires(Candidature $candidature): void
    {
        $offre = $candidature->getRefOffre();
        $candidat = $candidature->getRefUser();

        if (!$offre || !$candidat) {
            return;
        }

        $auteurOffre = $offre->getAuteur();
        if (!$auteurOffre) {
            return;
        }

        $entreprise = $auteurOffre->getrefEntreprise();
        if (!$entreprise) {
            return;
        }

        // Récupérer tous les partenaires de cette entreprise
        $partenaires = $this->getPartenairesFromEntreprise($entreprise->getUsers());

        if (empty($partenaires)) {
            return;
        }

        // Préparer les informations pour l'email
        $subject = sprintf(
            'Nouvelle candidature pour "%s"',
            $offre->getTitre()
        );

        $body = $this->buildEmailBody($candidature, $offre, $candidat, $entreprise);

        // Envoyer un email à chaque partenaire individuellement
        foreach ($partenaires as $partenaireEmail) {
            $email = (new Email())
                ->from($this->fromAddress)
                ->to($partenaireEmail)
                ->subject($subject)
                ->text($body);

            $this->mailer->send($email);
        }
    }

    /**
     * Récupère les emails de tous les utilisateurs ayant le rôle ROLE_PARTENAIRE
     * dans une collection d'utilisateurs.
     */
    private function getPartenairesFromEntreprise($users): array
    {
        $emails = [];

        foreach ($users as $user) {
            if ($user instanceof User && in_array('ROLE_PARTENAIRE', $user->getRoles(), true)) {
                $email = $user->getEmail();
                if ($email) {
                    $emails[] = $email;
                }
            }
        }

        return array_values(array_unique($emails));
    }

    /**
     * Construit le corps de l'email avec toutes les informations pertinentes.
     */
    private function buildEmailBody(Candidature $candidature, $offre, User $candidat, $entreprise): string
    {
        $candidatNom = $candidat->getNom() ?? '';
        $candidatPrenom = $candidat->getPrenom() ?? '';
        $candidatEmail = $candidat->getEmail() ?? '';
        $candidatMetier = $candidat->getMetier() ?? 'Non spécifié';

        $offreTitre = $offre->getTitre() ?? '';
        $offreType = $offre->getType() ?? '';
        $offreDescription = $offre->getDescription() ?? '';

        $entrepriseNom = $entreprise->getNom() ?? '';

        $lettreMotivation = $candidature->getLettre() ?? '';

        return <<<EMAIL
Bonjour,

Une nouvelle candidature a été soumise pour une offre de votre entreprise.

===== INFORMATIONS SUR L'OFFRE =====
Titre : {$offreTitre}
Type : {$offreType}
Description : {$offreDescription}
Entreprise : {$entrepriseNom}

===== INFORMATIONS SUR LE CANDIDAT =====
Nom : {$candidatNom}
Prénom : {$candidatPrenom}
Email : {$candidatEmail}
Métier : {$candidatMetier}

===== LETTRE DE MOTIVATION =====
{$lettreMotivation}

========================================

Vous pouvez consulter cette candidature en vous connectant à votre espace partenaire.

Cordialement,
L'équipe HSP
EMAIL;
    }
}
