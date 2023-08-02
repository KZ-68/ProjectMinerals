<?php

namespace App\Form;

use App\Entity\Color;
use App\Entity\Lustre;
use App\Entity\Mineral;
use App\Entity\Category;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class MineralType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Mineral Name',
                'attr' => [
                    'class' => 'form-control' 
                ]
            ])
            ->add('formula', TextType::class, [
                'label' => 'Formula',
                'attr' => [
                    'class' => 'form-control' 
                ]
            ])
            ->add('crystal_system', TextType::class, [
                'label' => 'Crystal System',
                'attr' => [
                    'class' => 'form-control' 
                ]
            ])
            ->add('density', NumberType::class, [
                'scale' => 2
            ])
            ->add('hardness', IntegerType::class, [
                'label' => 'Hardness Scale',
                'empty_data' => 1,
                'attr' => [
                    'class' => 'form-control',
                    'min' => 1, 'max' => 100
                ]
            ])
            ->add('fracture', TextType::class, [
                'label' => 'Fracture',
                'attr' => [
                    'class' => 'form-control' 
                ]
            ])
            ->add('streak', TextType::class, [
                'label' => 'Streak',
                'attr' => [
                    'class' => 'form-control' 
                ]
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name'
            ])
            ->add('colors',  EntityType::class, [
                'class' => Color::class,
                'choice_label' => 'name',
                'expanded'  => true,
                'multiple'  => true,
            ])
            ->add('lustres',  EntityType::class, [
                'class' => Lustre::class,
                'choice_label' => 'type',
                'expanded'  => true,
                'multiple'  => true,
            ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-success' 
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Mineral::class,
        ]);
    }
}
