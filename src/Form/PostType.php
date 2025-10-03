<?php

namespace App\Form;

use App\Entity\Canal;
use App\Entity\Post;
use App\Entity\Reponse;
use App\Entity\user;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre')
            ->add('texte')
            ->add('dateHeure')
            ->add('refReponse', EntityType::class, [
                'class' => Reponse::class,
                'choice_label' => 'id',
            ])
            ->add('refCanal', EntityType::class, [
                'class' => Canal::class,
                'choice_label' => 'id',
            ])
            ->add('refUser', EntityType::class, [
                'class' => user::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
