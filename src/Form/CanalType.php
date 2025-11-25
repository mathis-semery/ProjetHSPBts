<?php

namespace App\Form;

use App\Entity\Canal;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class CanalType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', null, [
                'label' => 'Nom du canal',
                'attr' => ['class' => 'form-control form-control-solid', 'placeholder' => 'Ex : Général, Annonces...'],
            ])
            ->add('description', null, [
                'label' => 'Description',
                'attr' => ['class' => 'form-control form-control-solid', 'placeholder' => 'Décrivez le but du canal...'],
            ])
            ->add('ListeAuto', ChoiceType::class, [
                'choices'  => [
                    'Médecin' => 'MEDECIN',
                    'Partenaire' => 'PARTENAIRE',
                    'Etudiant' => 'ETUDIANT',
                ],
                'multiple' => true,  // permet de sélectionner plusieurs rôles
                'expanded' => true, // true = checkboxes, false = select multiple
                'required' => false,
                'label' => 'Liste automatique (Rôles)',
                'attr' => ['class' => 'form-select'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Canal::class,
        ]);
    }
}
