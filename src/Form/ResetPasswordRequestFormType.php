<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class ResetPasswordRequestFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('email', EmailType::class, [
            'label' => "Saisi ton adresse e-mail, afin de recevoir un lien de réinitialisation.",
            'label_attr' =>['class'=> 'fs-5 d-flex text-center sjustify-content-center'],
            'attr' => [
                'autocomplete' => 'email',
                'class' => 'font-weight-light text-center',
                'placeholder' => 'alice@delabas.fr',
            ],
            'constraints' => [
                new NotBlank([
                    'message' => 'Merci de saisir ton adresse mail',
                ]),
            ],
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
        ]);
    }
}
