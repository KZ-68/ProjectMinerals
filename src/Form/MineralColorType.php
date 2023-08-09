<?php

namespace App\Form;

use App\Entity\Color;
use App\Entity\Mineral;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class MineralColorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
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
