<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entrez le nom de l\'article'
                ],
                'label' => 'Nom de l\'article'
            ])
            ->add('prix', NumberType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entrez le prix',
                    'step' => '0.01'
                ],
                'label' => 'Prix (€)',
                'html5' => true
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'titre',
                'attr' => ['class' => 'form-select'],
                'label' => 'Catégorie',
                'placeholder' => 'Choisissez une catégorie'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}