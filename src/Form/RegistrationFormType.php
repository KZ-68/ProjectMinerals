<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class RegistrationFormType extends AbstractType
{
    private $router;

    function __construct(RouterInterface $router){
        $this->router = $router;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $privacy = $this->router->generate('privacy_policy');

        $builder
            // Champ username de type texte
            ->add('username', TextType::class, [
                // L'attribut de classe nommé
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            // Champ username de type email
            ->add('email', EmailType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                // Les contraintes du champ
                'constraints' => [
                    // Le champ ne peut pas être vide.
                    new NotBlank([
                        'message' => 'Please enter your email',
                    ]),
                ]
            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => [
                    'autocomplete' => 'new-password',
                    'class' => 'form-control',
                    'onkeyup' => 'checkPasswordStrengthRegister()'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 12,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                    new Regex([
                        'pattern' => '/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{12,}$/',
                        'match' => true,
                        'message' => 'This password is not valid.'
                    ])
                ],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'label' => 'I accept the <a href="' . $privacy .'" target="_blank">Privacy Policy</a> and the Terms of Use',
                'label_html' => true,
                'required' => true,
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'We need your consent for the registration',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
