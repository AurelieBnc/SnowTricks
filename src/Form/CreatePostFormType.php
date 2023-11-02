<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Post;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class CreatePostFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'required' => true,
                'label' => 'Titre', 
                'label_attr' =>['class'=> 'fw-bold fs-2 d-flex justify-content-center'],
                'attr' => [
                    'placeholder' => 'De quel trick vas tu parlé?',
                    'class' => 'text-center'
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Merci de renseigner un titre pour l\'article.',]) 
                ],
            ])
            ->add('content', TextareaType::class, [
                'label'=> 'Contenu',
                'required' => true,
                'attr'=> [
                    'placeholder' => 'Mon nouveau contenu ... ','row'=> 10,
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Merci de renseigner un contenu.',]) 
                ],
            ])
            ->add('pictureList', FileType::class, [
                'label' => 'Télécharges tes images',
                'multiple' => true,
                'mapped' => false,
                'required' => false,
            ] )
            ->add('media', TextType::class, [
                'label' => 'Ajoutes ton lien Url Vidéo',
                'mapped' => false,
                'required' => false,
            ] )
            ->add('category', EntityType::class, [
                'label' => 'Choisis la catégorie de l\'article',
                'class' => Category::class,
                'choice_label' => 'title',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
