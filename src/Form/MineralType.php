<?php

namespace App\Form;

use App\Entity\Color;
use App\Entity\Lustre;
use App\Entity\Mineral;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
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
            ->add('colors', EntityType::class, [
                'class' => Color::class,
                'choice_label' => function ($allChoices, $currentChoiceKey)
                {
                    return $allChoices->getName();
                },
                'expanded'  => false,
                'multiple'  => true,
                'by_reference' => false,
                'attr' => [
                    'class' => 'form-select'
                ]
            ])
            ->add('lustres', EntityType::class, [
                'class' => Lustre::class,
                'choice_label' => function ($allChoices, $currentChoiceKey)
                {
                    return $allChoices->getType();
                },
                'expanded'  => true,
                'multiple'  => true,
                'by_reference' => false
            ])
            ->add('images', FileType::class, [
                'label' => false,
                'multiple' => true,
                'mapped' => false,
                'required' => false,
                'constraints' => [
                new All ([
                    new File([
                        'maxSize' => '5000k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/jpg',
                            'image/png',
                            'image/webp',
                            'image/x-icon',
                            'image/tiff',
                            'image/bmp'
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image format',
                    ])
                ])
                    
            ],
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
