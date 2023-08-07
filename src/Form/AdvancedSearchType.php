<?php

namespace App\Form;

use App\Model\AdvancedSearchData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class AdvancedSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('formula', TextType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('crystal_system', TextType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('density', NumberType::class, [
                'scale' => 2,
                'required' => false,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('hardness', IntegerType::class, [
                'label' => 'Hardness Scale',
                'empty_data' => 1,
                'attr' => [
                    'class' => 'form-control',
                    'min' => 1, 'max' => 100
                ],
                'required' => false
            ])
            ->add('fracture', TextType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('streak', TextType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AdvancedSearchData::class,
            'method' => 'GET',
            'csrf_protection' => false
        ]);
    }
}
