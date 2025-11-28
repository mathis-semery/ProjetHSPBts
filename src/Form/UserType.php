<?php

namespace App\Form;

use App\Entity\Entreprise;
use App\Entity\Etablissement;
use App\Entity\Hopital;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;



class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($options['profile_mode']) {
            $builder
                ->add('email')
                ->add('nom')
                ->add('prenom')
                ->add('password', PasswordType::class, [
                    'required' => false,
                    'mapped' => true,
                    'attr' => ['placeholder' => '********'],
                ])
                ->add('cvNomFichier', FileType::class, [
                    'label' => 'CV (PDF)',
                    'mapped' => false,
                    'required' => false,
                    'constraints' => [
                        new File([
                            'maxSize' => '10M',
                            'mimeTypes' => [
                                'application/pdf',
                                'application/x-pdf',
                            ],
                            'mimeTypesMessage' => 'Veuillez uploader un fichier PDF valide',
                        ])
                    ],
                ]);
        } else {
            $builder
                ->add('email')
                ->add('nom')
                ->add('prenom')
                ->add('cvNomFichier', FileType::class, [
                    'label' => 'CV (PDF)',
                    'mapped' => false,
                    'required' => false,
                    'constraints' => [
                        new File([
                            'maxSize' => '100M',
                            'mimeTypes' => [
                                'application/pdf',
                                'application/x-pdf',
                            ],
                            'mimeTypesMessage' => 'Veuillez uploader un fichier PDF valide',
                        ])
                    ],
                ])
                ->add('rolesForForm', ChoiceType::class, [
                    'label' => 'Rôles',
                    'choices' => [
                        'Administrateur' => 'ROLE_ADMIN',
                        'Élève/Étudiant' => 'ROLE_ELEVE',
                        'Médecin' => 'ROLE_MEDECIN',
                        'Partenaire' => 'ROLE_PARTENAIRE',
                    ],
                    'multiple' => true,
                    'expanded' => true,
                    'mapped' => false,
                ])
                    ->add('email')
                    ->add('nom')
                    ->add('prenom')
                    ->add('cvNomFichier', FileType::class, [
                        'label' => 'CV (PDF)',
                        'mapped' => false,
                        'required' => false,
                        'constraints' => [
                            new File([
                                'maxSize' => '100M',
                                'mimeTypes' => [
                                    'application/pdf',
                                    'application/x-pdf',
                                ],
                                'mimeTypesMessage' => 'Veuillez uploader un fichier PDF valide',
                            ])
                        ],
                    ])
                    ->add('password', \Symfony\Component\Form\Extension\Core\Type\PasswordType::class, [
                        'mapped' => true,
                        'required' => false,
                        'attr' => [
                            'placeholder' => '********',
                        ],
                    ])
                    ->add('metier', \Symfony\Component\Form\Extension\Core\Type\TextType::class, [
                        'label' => 'Métier (à titre indicatif)',
                        'required' => false,
                        'attr' => [
                            'placeholder' => 'Ex: Élève, Médecin, Partenaire...',
                        ],
                    ])
                    ->add('formation')
                    ->add('specialite')
                    ->add('posteOccupe')
                    ->add('dateCreation')
                    ->add('etat_validation', CheckboxType::class, [
                        'required' => false,
                        'label' => 'Validé ?',
                    ])


                    ->add('refHopital', EntityType::class, [
                        'class' => Hopital::class,
                        'choice_label' => 'nom',
                        'required' => false,
                    ])
                    ->add('refEtablissement', EntityType::class, [
                        'class' => Etablissement::class,
                        'choice_label' => 'nom',
                        'required' => false,
                    ])
                    ->add('refEntreprise', EntityType::class, [
                        'class' => Entreprise::class,
                        'choice_label' => 'nom',
                        'required' => false,
                    ])

                ;

        }




    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'profile_mode' => false,
        ]);
    }


    public function finishView(\Symfony\Component\Form\FormView $view, \Symfony\Component\Form\FormInterface $form, array $options): void
    {
        $user = $form->getData();
        if ($user instanceof User && isset($view['rolesForForm'])) {
            $view['rolesForForm']->vars['data'] = $user->getRoles();
        }
    }

}
