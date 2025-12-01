<?php

namespace App\Form;

use App\Entity\Offre;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OffreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'label' => 'Titre de l\'offre',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex: Développeur Web Junior'
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 5,
                    'placeholder' => 'Décrivez l\'offre en détail...'
                ]
            ])
            ->add('mission', TextareaType::class, [
                'label' => 'Missions',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 4,
                    'placeholder' => 'Liste des missions principales...'
                ]
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Type de contrat',
                'choices' => [
                    'Stage' => 'Stage',
                    'Alternance' => 'Alternance',
                    'CDD' => 'CDD',
                    'CDI' => 'CDI'
                ],
                'attr' => [
                    'class' => 'form-select'
                ]
            ])
            ->add('salaire', MoneyType::class, [
                'label' => 'Salaire (optionnel)',
                'required' => false,
                'currency' => 'EUR',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex: 35000'
                ]
            ])
            ->add('etat', ChoiceType::class, [
                'label' => 'État de l\'offre',
                'choices' => [
                    'Ouverte' => true,
                    'Fermée' => false
                ],
                'attr' => [
                    'class' => 'form-select'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Offre::class,
        ]);
    }
}
