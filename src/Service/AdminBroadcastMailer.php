<?php

namespace App\Service;

use App\Dto\SimpleUpload;
use App\Repository\UserRepository;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AdminBroadcastMailer
{
    public function __construct(
        private MailerInterface $mailer,
        private UserRepository $users,
        private string $fromAddress,
    ) {}

    /**
     * Envoie toutes les pièces jointes à tous les utilisateurs ROLE_ADMIN.
     * Garde nom/extension d’origine.
     */
    public function sendUploads(?object $user, SimpleUpload $data): void
    {
        $adminEmails = $this->findAdminEmails();
        if (!$adminEmails) {
            $adminEmails = [$this->fromAddress]; // fallback
        }

        $replyTo = null;
        $metier  = null;

        if ($user) {
            $replyTo = method_exists($user, 'getEmail') ? $user->getEmail() : null;
            $metier  = method_exists($user, 'getMetier') ? $user->getMetier() : null;
        }

        $subject = sprintf(
            'Vérification%s%s',
            $metier ? " ({$metier})" : '',
            $replyTo ? " — {$replyTo}" : ''
        );

        $email = (new Email())
            ->from($this->fromAddress)
            ->subject($subject)
            ->text(sprintf(
                "Nouveaux fichiers de vérification.\n- Métier: %s\n- Email: %s\n\nFichiers en pièces jointes.",
                (string)($metier ?? 'n/d'),
                (string)($replyTo ?? 'n/d')
            ));

        // destinataires : TO le 1er admin, BCC les autres
        $email->to($adminEmails[0]);
        if (\count($adminEmails) > 1) {
            $email->bcc(...\array_slice($adminEmails, 1));
        }
        if ($replyTo) {
            $email->replyTo($replyTo);
        }

        foreach ($data->attachments as $file) {
            if (!$file instanceof UploadedFile) continue;
            $name = $this->sanitizeFilename($file->getClientOriginalName() ?: 'fichier');
            $mime = $file->getMimeType() ?: 'application/octet-stream';
            $email->attachFromPath($file->getPathname(), $name, $mime);
        }

        $this->mailer->send($email);
    }

    /** Emails de tous les users ayant "ROLE_ADMIN" dans le JSON roles */
    private function findAdminEmails(): array
    {
        $rows = $this->users->createQueryBuilder('u')
            ->select('u.email')
            ->where('u.roles LIKE :needle')
            ->andWhere('u.email IS NOT NULL')
            ->setParameter('needle', '%"ROLE_ADMIN"%')
            ->getQuery()
            ->getArrayResult();

        $emails = [];
        foreach ($rows as $r) {
            $e = (string)($r['email'] ?? '');
            if ($e !== '') $emails[] = $e;
        }
        return \array_values(\array_unique($emails));
    }

    private function sanitizeFilename(string $name): string
    {
        $name = str_replace(["\r", "\n"], '', $name);
        $name = preg_replace('/[^\w\.\-\s]/u', '_', $name) ?? $name;
        return $name !== '' ? $name : 'fichier';
    }
}
