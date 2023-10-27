<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ResetPasswordRequestFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => "Saisi ton adresse e-mail et nous t'enverrons un lien pour réinitialiser ton mot de passe",
                'label_attr' =>['class'=> 'fw-bold fs-4 d-flex text-center sjustify-content-center'],
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
        $resolver->setDefaults([]);
    }
}
