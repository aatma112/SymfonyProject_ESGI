<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label'=> 'Titre',
                'attr'=> [
                    'placeholder'=> 'title de l\'article',
                    'row'=> 10
                ]
            ])
            ->add('content')
            ->add( 'enabled', CheckboxType::class,
            ['label' =>'Publié']);

            if ($options['isEdit']) {
                $builder
                ->add('user', EntityType::class, [
                    'class'=> User::class, 
                    'choice_label' => 'fullName',
                    'label' => 'Auteur',
                    'multiple' => false,
                    'expanded' => false,
                ]);
            }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class, 
            'isEdit' => false,
        ]);
    }
}