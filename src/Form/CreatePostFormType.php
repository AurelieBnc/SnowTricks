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
                'label' => 'Titre de l\'article', 
                'label_attr' =>['class'=> 'fw-bold pb-2 mt-3'],
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
                'label' => false,
                'required' => true,
                'attr'=> [
                    'placeholder' => 'Mon nouveau contenu ... ','row'=> 50,
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Merci de renseigner un contenu.',]) 
                ],
            ])
            ->add('pictureList', FileType::class, [
                'label' => 'Télécharges tes images',
                'label_attr' =>['class'=> 'fw-bold pb-2 mt-3'],
                'multiple' => true,
                'mapped' => false,
                'required' => false,
            ] )
            ->add('media', TextType::class, [
                'label' => 'Ajoutes ton lien Url Vidéo',
                'label_attr' =>['class'=> 'fw-bold pb-2 mt-3'],
                'mapped' => false,
                'required' => false,
            ] )
            ->add('category', EntityType::class, [
                'label' => 'Choisis la catégorie de l\'article',
                'label_attr' =>['class'=> 'fw-bold pb-2 mt-3'],
                'class' => Category::class,
                'choice_label' => 'title',
                'attr' => [
                    'class' => 'text-center col-sm-10 col-lg-4'
                ],
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
