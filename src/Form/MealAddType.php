<?php

namespace App\Form;

use App\Entity\Meal;
use phpDocumentor\Reflection\Types\ClassString;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class MealAddType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ['constraints' => [
//                    new Length([
//                        'min' => 5,
//                        'minMessage' => 'Должно быть больше 5 символов'
//                    ])
            ]
            ])
            ->add('category', TextType::class, [
                    'constraints' =>
                        new NotBlank([
                                'message' => 'Поле не может быть пустым!']
                        ),
                    'required' => false
                ]
            )
            ->add('img', FileType::class, [
                'label' => 'Image',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new NotBlank(['message' => 'Поле не может быть пустым!'])
                ],
                'attr' => [
                    'placeholder' => 'Image'
                ]
            ])
            ->add('description');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Meal::class,
        ]);
    }
}