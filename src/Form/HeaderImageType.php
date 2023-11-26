<?php

namespace App\Form;

use App\Entity\Trick;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HeaderImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('headerImage', ChoiceType::class, [
                // $builder->getData(): permet de récupérer l'objet associé au formulaire via les data_class
                'choices' => $this->getPictureChoices($builder->getData()),
                'choice_label' => null,
                'choice_attr' => ['class' => 'd-none'],
                'expanded' => true,
                'multiple' => false,
                'required' => true,
                'label' => 'Choisis ton image d\'en-tête',
                'label_attr' => ['class'=> 'fw-bold pb-2 mt-3 mb-5'],
            ]);
    }

    private function getPictureChoices(Trick $trick): array
    {
        $pictureList = $trick->getPictureList();
        $choices = [];

        foreach ($pictureList as $picture) {
            // value + label
            $choices[$picture->getName()] = $picture->getName();
        }

        return $choices;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
        ]);
    }
}
