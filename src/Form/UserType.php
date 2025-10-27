<?php

namespace App\Form;

use App\Entity\Etablissement;
use App\Entity\Hopital;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('rolesForForm', ChoiceType::class, [
                'label' => 'Rôles',
                'choices' => [
                    'Utilisateur' => 'ROLE_USER',
                    'Administrateur' => 'ROLE_ADMIN',
                ],
                'multiple' => true,
                'expanded' => true,
                'mapped' => false, // on gère manuellement
            ])
            ->add('password')
            ->add('nom')
            ->add('prenom')
            ->add('metier')
            ->add('etat_validation', CheckboxType::class, [
                'required' => false,
                'label' => 'Validé ?',
            ])
            ->add('dateCreation')
            ->add('formation')
            ->add('cv')
            ->add('specialite')
            ->add('posteOccupe')
            ->add('refHopital', EntityType::class, [
                'class' => Hopital::class,
                'choice_label' => 'nom', // mieux que 'id'
                'required' => false,
            ])
            ->add('refEtablissement', EntityType::class, [
                'class' => Etablissement::class,
                'choice_label' => 'nom',
                'required' => false,
            ])
            ->add('refEntreprise', EntityType::class, [
                'class' => Etablissement::class,
                'choice_label' => 'nom',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }

    // Gérer manuellement le champ rolesForForm
    public function finishView(\Symfony\Component\Form\FormView $view, \Symfony\Component\Form\FormInterface $form, array $options): void
    {
        $user = $form->getData();
        if ($user instanceof User) {
            $view['rolesForForm']->vars['data'] = $user->getRoles();
        }
    }
}
