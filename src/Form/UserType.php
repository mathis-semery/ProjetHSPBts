<?php

namespace App\Form;

use App\Entity\Etablissement;
use App\Entity\Hopital;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('roles')
            ->add('password')
            ->add('nom')
            ->add('prenom')
            ->add('metier')
            ->add('etat_validation')
            ->add('dateCreation')
            ->add('formation')
            ->add('cv')
            ->add('specialite')
            ->add('posteOccupe')
            ->add('refHopital', EntityType::class, [
                'class' => Hopital::class,
                'choice_label' => 'id',
            ])
            ->add('refEtablissement', EntityType::class, [
                'class' => Etablissement::class,
                'choice_label' => 'id',
            ])
            ->add('refEntreprise', EntityType::class, [
                'class' => Etablissement::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
