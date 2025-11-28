<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class)
            ->add('nom', TextType::class)
            ->add('prenom', TextType::class)
            ->add('metierRole', ChoiceType::class, [
                'label' => 'Votre métier',
                'mapped' => false,
                'choices' => [
                    'Élève/Étudiant' => 'ROLE_ELEVE',
                    'Médecin' => 'ROLE_MEDECIN',
                    'Partenaire' => 'ROLE_PARTENAIRE',
                ],
                'placeholder' => 'Choisissez votre métier',
                'required' => true,
            ])
            ->add('metier', TextType::class, [
                'label' => 'Précisez votre métier (optionnel)',
                'required' => false,
                'attr' => ['placeholder' => 'Ex: Infirmier, Étudiant en médecine...'],
            ])
            ->add('formation', TextType::class, ['required' => false])
            ->add('specialite', TextType::class, ['required' => false])
            ->add('poste_occupe', TextType::class, ['required' => false])
            ->add('cv', TextType::class, ['required' => false, 'label' => 'CV (Nom du fichier ou lien)'])
            ->add('plainPassword', PasswordType::class, [
                'mapped' => false,
                'constraints' => [
                    new NotBlank(['message' => 'Merci de saisir un mot de passe']),
                    new Length(['min' => 6, 'minMessage' => 'Au moins {{ limit }} caractères']),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
