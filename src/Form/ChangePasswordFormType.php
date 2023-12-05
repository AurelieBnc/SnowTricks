<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ChangePasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('plainPassword', RepeatedType::class, [
            'type' => PasswordType::class,
            'first_options' => [
                'constraints' => [
                    new Assert\Type('string'),
                    new Assert\Length([
                        'min' => 12,
                        'minMessage' => 'Le mot de passe doit contenir au moins 12 caractères.',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                    // Password must contain at least a upper and lower case
                    new Assert\Regex([
                        'pattern' => '/(?=.*[a-z])(?=.*[A-Z])/',
                        'message' => 'Le mot de passe doit contenir au moins une majuscule et une minuscule.',
                        'match' => true,
                    ]),
                    // Password must contain at least one digit
                    new Assert\Regex([
                        'pattern' => '/\d+/i',
                        'message' => 'Le mot de passe doit contenir au moins un chiffre.',
                        'match' => true,
                    ]),
                    // Password must contain at least one special char from the list (including space)
                    new Assert\Regex([
                        'pattern' => '/[^a-zA-Z0-9\n\r]+/i',
                        'message' => 'Le mot de passe doit contenir au moins un caractère spécial.',
                        'match' => true,
                    ]),
                ],
                'label' => 'Mot de passe',
                'label_attr' =>['class'=> 'fw-bold fs-2 d-flex justify-content-center'],
                'attr' => [
                    'autocomplete' => 'new-password',
                    'class' => 'font-weight-light text-center',
                    'placeholder' => 'Mot de passe sécurisé',
                ],
            ],
            'second_options' => [
                'label' => 'Répéter le mot de passe',
                'label_attr' =>['class'=> 'fw-bold fs-2 d-flex justify-content-center'],
                'attr' => [
                    'class' => 'font-weight-light text-center',
                    'placeholder' => 'Mot de passe identique',
                ],
            ],
            'invalid_message' => 'Les mots de passe doivent être identique.',
            'mapped' => false,
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
        ]);
    }
}
