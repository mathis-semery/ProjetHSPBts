<?php

namespace App\Form;

use App\Entity\Canal;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CanalType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom du canal',
                'attr' => ['placeholder' => 'Ex : Général, Annonces...']
            ])
            ->add('description', TextType::class, [
                'label' => 'Description',
                'attr' => ['placeholder' => 'Décrivez le canal...']
            ])
            ->add('ListeAuto', ChoiceType::class, [
                'label' => 'Rôles et spécialités autorisés',
                'choices' => [
                    'Médecin' => 'MEDECIN',
                    'Partenaire' => 'PARTENAIRE',
                    'Etudiant' => 'ETUDIANT',
                    'Cardiologie' => 'CARDIOLOGIE',
                    'Neurologie' => 'NEUROLOGIE',
                    'Dermatologie' => 'DERMATOLOGIE',
                ],
                'multiple' => true,
                'expanded' => false,
                'attr' => ['class' => 'form-select', 'size' => 8]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Canal::class,
        ]);
    }
}
