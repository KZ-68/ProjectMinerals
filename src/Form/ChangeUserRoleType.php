<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChangeUserRoleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $permissions = [
            'User role' => 'ROLE_USER' ,
            'Moderator role' => 'ROLE_MODERATOR'
        ];
        
        $builder
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => function ($allChoices, $currentChoiceKey)
                {
                    return $allChoices->getUserName();
                },
                'expanded'  => false,
                'multiple'  => false,
                'label'    => 'Choose the user',
                'attr' => [
                    'class' => 'form-single-select'
                ]
            ])
            ->add('roles', ChoiceType::class, [
                'label'   => 'Choose the role',
                'choices' => $permissions,
                'attr' => [
                    'class' => 'form-single-select'
                ]
            ])
            ->add(
                'save', SubmitType::class
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
       
    }
}
