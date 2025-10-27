<?php

namespace App\Controller;

use App\Dto\SimpleUpload;
use App\Form\VerificationUploadType;
use App\Service\AdminBroadcastMailer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VerificationController extends AbstractController
{
    /**
     * Limite Gmail = ~25 Mo message total (après encodage).
     * L’encodage base64 grossit d’environ 33 %, donc on fixe ~18 Mo BRUTS.
     */
    private const RAW_ATTACHMENTS_LIMIT_BYTES = 18 * 1024 * 1024; // 18 MiB

    #[Route('/verification', name: 'app_verification_request')]
    public function requestVerification(Request $request, AdminBroadcastMailer $sender): Response
    {
        $dto  = new SimpleUpload();
        $user = $this->getUser();

        // Récupère le métier de l'utilisateur et construit les consignes adaptées
        $metierRaw = null;
        if ($user && method_exists($user, 'getMetier')) {
            $metierRaw = (string) $user->getMetier();
        }
        [$pageTitle, $guidanceTitle, $guidanceItems] = $this->guidanceForMetier($metierRaw);

        $form = $this->createForm(VerificationUploadType::class, $dto, [
            'attr' => ['id' => 'verify-form'],
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                // 1) Vérifier la TAILLE TOTALE (brut) ≤ ~18 Mo
                $total = 0;
                foreach ($dto->attachments as $file) {
                    $total += (int) ($file?->getSize() ?? 0);
                }
                if ($total > self::RAW_ATTACHMENTS_LIMIT_BYTES) {
                    $this->addFlash(
                        'danger',
                        sprintf(
                            "Taille totale des fichiers (%.2f Mo) trop élevée. Limite ≈ 18 Mo bruts (≈ 25 Mo email après encodage).",
                            $this->toMega($total)
                        )
                    );
                    return $this->redirectToRoute('app_verification_request');
                }

                // 2) Envoi
                try {
                    $sender->sendUploads($user, $dto);
                    $this->addFlash('success', 'Fichiers envoyés aux administrateurs.');
                } catch (\Throwable $e) {
                    $this->addFlash('danger', 'Erreur d’envoi : '.$e->getMessage());
                }
                return $this->redirectToRoute('app_verification_request');
            } else {
                $this->addFlash('danger', 'Le formulaire est invalide : vérifie les fichiers.');
            }
        }

        return $this->render('verification/request.html.twig', [
            'form'            => $form->createView(),
            'page_title'      => $pageTitle,
            'guidance_title'  => $guidanceTitle,
            'guidance_items'  => $guidanceItems,
            'raw_limit_mb'    => $this->toMega(self::RAW_ATTACHMENTS_LIMIT_BYTES),
        ]);
    }

    private function toMega(int $bytes): float
    {
        return $bytes / (1024 * 1024);
    }

    /**
     * Construit le titre et les consignes selon le métier.
     * @return array{0:string,1:string,2:string[]} [pageTitle, guidanceTitle, guidanceItems]
     */
    private function guidanceForMetier(?string $metierRaw): array
    {
        $key = $this->normalizeMetier($metierRaw);

        // Défauts “partenaire”
        $pageTitle     = "Envoyer des documents de vérification";
        $guidanceTitle = "Documents attendus pour un partenaire";
        $guidanceItems = [
            "Pièce d’identité (CNI ou passeport)",
            "Kbis (moins de 3 mois) OU avis Sirene (INSEE) OU récépissé RNA (associations)",
        ];

        if ($key === 'etudiant') {
            $pageTitle     = "Informations nécessaires à l'inscription d’un étudiant";
            $guidanceTitle = "Documents attendus pour un étudiant";
            $guidanceItems = [
                "Pièce d’identité (CNI ou passeport)",
                "Certificat de scolarité (année en cours) OU carte étudiante (recto-verso)",
            ];
        } elseif ($key === 'medecin') {
            $pageTitle     = "Informations nécessaires à la vérification d’un médecin";
            $guidanceTitle = "Documents attendus pour un médecin";
            $guidanceItems = [
                "Pièce d’identité (CNI ou passeport)",
                "Attestation d’inscription à l’Ordre (moins de 6 mois) OU carte CPS / e-CPS",
            ];
        } elseif ($key === 'partenaire') {
            $pageTitle = "Envoyer des documents pour un partenaire";
        }

        return [$pageTitle, $guidanceTitle, $guidanceItems];
    }

    /**
     * Normalise le métier : etudiant | medecin | partenaire | null
     */
    private function normalizeMetier(?string $raw): ?string
    {
        if (!$raw) return null;
        $s = mb_strtolower(trim($raw), 'UTF-8');

        if (preg_match('/etudiant|étudiant|student|eleve|élève/', $s)) return 'etudiant';
        if (preg_match('/medecin|médecin|doctor|docteur/', $s)) return 'medecin';
        if (preg_match('/partenaire|partner|entreprise|association|asso/', $s)) return 'partenaire';

        return null;
    }
}
