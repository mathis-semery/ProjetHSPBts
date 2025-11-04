<?php

namespace App\Form;

use App\Entity\Canal;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CanalType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom du canal',
                'attr' => ['placeholder' => 'Entrez le nom du canal']
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => ['placeholder' => 'Description du canal', 'rows' => 4]
            ])
            ->add('ListeAuto', TextType::class, [
                'label' => 'Liste automatique (métiers)',
                'required' => false,
                'attr' => ['placeholder' => 'Ex: Medecin, Infirmier, Administratif']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Canal::class,
        ]);
    }
}
