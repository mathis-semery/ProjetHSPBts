<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class VerificationRequest
{
    #[Assert\Choice(['etudiant','medecin','partenaire'])]
    #[Assert\NotBlank]
    public ?string $profile = null;

    // Renseignés si user NON connecté (optionnels)
    #[Assert\Length(max: 100)]
    public ?string $firstName = null;

    #[Assert\Length(max: 100)]
    public ?string $lastName = null;

    #[Assert\Email]
    #[Assert\Length(max: 180)]
    public ?string $email = null;

    /** Pièce d’identité (CNI/Passeport) */
    /** @var UploadedFile|null */
    #[Assert\NotNull(message: 'La pièce d’identité est obligatoire.')]
    #[Assert\File(
        maxSize: '10M',
        mimeTypes: ['application/pdf','image/jpeg','image/png','image/heic','image/heif'],
        mimeTypesMessage: 'Formats acceptés : PDF, JPG, PNG, HEIC.'
    )]
    public ?UploadedFile $identityDocument = null;

    /** Document métier unique selon le profil */
    /** @var UploadedFile|null */
    #[Assert\NotNull(message: 'Le document justificatif est obligatoire.')]
    #[Assert\File(
        maxSize: '10M',
        mimeTypes: ['application/pdf','image/jpeg','image/png','image/heic','image/heif'],
        mimeTypesMessage: 'Formats acceptés : PDF, JPG, PNG, HEIC.'
    )]
    public ?UploadedFile $businessDocument = null;

    #[Assert\IsTrue(message: 'Vous devez accepter l’envoi de ces documents.')]
    public bool $consent = false;
}
