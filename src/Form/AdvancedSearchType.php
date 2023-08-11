<?php

namespace App\Form;

use App\Entity\Color;
use App\Entity\Lustre;
use App\Entity\Variety;
use App\Entity\Category;
use App\Model\AdvancedSearchData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class AdvancedSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
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
                'attr' => [
                    'class' => 'form-control',
                    'min' => 1, 'max' => 9
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
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'required' => false
            ])
            ->add('varieties', EntityType::class, [
                'class' => Variety::class,
                'choice_label' => function ($allChoices, $currentChoiceKey)
                {
                    return $allChoices->getName();
                },
                'expanded'  => false,
                'multiple'  => true,
                'by_reference' => false,
                'required' => false
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
                'required' => false
            ])
            ->add('lustres', EntityType::class, [
                'class' => Lustre::class,
                'choice_label' => function ($allChoices, $currentChoiceKey)
                {
                    return $allChoices->getType();
                },
                'expanded'  => false,
                'multiple'  => true,
                'by_reference' => false,
                'required' => false
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
