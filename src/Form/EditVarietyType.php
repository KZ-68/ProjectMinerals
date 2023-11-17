<?php

namespace App\Form;

use App\Entity\Variety;
use Symfony\Component\Form\AbstractType;
use Symfony\UX\Dropzone\Form\DropzoneType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class EditVarietyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Variety Name',
                'attr' => [
                    'class' => 'form-control' 
                ]
            ])
            ->add('image_presentation', DropzoneType::class, [
                'label' => 'Image Presentation',
                'multiple' => false,
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'class' => 'form-file'
                ],
                'constraints' => [
                    new Image ([
                        'maxSize' => '5000k',
                        "maxSizeMessage" => 'The size of this image is too big, 
                        the maximum size autorized is {{ limit }} {{ suffix }}'
                    ]),
                    new File([
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
                ],
            ])
            ->add('region_name', TextType::class, [
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'class' => 'form-control' 
                ]
            ])
            ->add('latitude', TextType::class, [
                'mapped' => false,
                'attr' => [
                    'readonly' => true,
                    'class' => 'form-control'
                ]
            ])
            ->add('longitude', TextType::class, [
                'mapped' => false,
                'attr' => [
                    'readonly' => true,
                    'class' => 'form-control'
                ]
            ])
            ->add('images', DropzoneType::class, [
                'label' => 'Images Collection',
                'multiple' => true,
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new All ([
                        new Image ([
                            'maxSize' => '5000k',
                            "maxSizeMessage" => 'The size of this image is too big, 
                            the maximum size autorized is {{ limit }} {{ suffix }}'
                        ]),
                        new File([
                            'maxSize' => '2048k',
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
            'data_class' => Variety::class,
        ]);
    }
}
