<?php

namespace App\Form;

use App\Dto\SimpleUpload;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class VerificationUploadType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('attachments', FileType::class, [
            'label'       => 'Fichiers (images / PDF)',
            'mapped'      => true,
            'required'    => true,
            'multiple'    => true,
            'constraints' => [
                new Assert\Count(min: 1, minMessage: 'Ajoutez au moins un fichier.'),
                new Assert\All([
                    new Assert\File(
                    // Limite PAR FICHIER (18 Mo bruts)
                        maxSize: '18M',
                        mimeTypes: [
                            'application/pdf',
                            'image/jpeg',
                            'image/png',
                            'image/heic',
                            'image/heif',
                        ],
                        mimeTypesMessage: 'Formats acceptés : PDF, JPG, PNG, HEIC.'
                    ),
                ]),
            ],
            'help' => 'Vous pouvez sélectionner plusieurs fichiers. Limite par fichier : 18 Mo.',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SimpleUpload::class,
        ]);
    }
}
