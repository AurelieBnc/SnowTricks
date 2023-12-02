<?php

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use App\Entity\Picture;

class PictureTransformer implements DataTransformerInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * Transforms an object (Picture) to a string (name).
     * 
     * @param  Picture|null $picture
     */
    public function transform($picture): string
    {
        if (!$picture instanceof Picture) {
            return throw new TransformationFailedException('Les données ne correspondent pas à une Picture.');
        }

        return $picture->getName();
    }

    /**
     * Transforms a string (name) to an object (picture).
     *
     * @param  string $name
     * @throws TransformationFailedException if object (picture) is not found.
     */
    public function reverseTransform($name): ?Picture
    {
        if (!$name) {
            return throw new TransformationFailedException('Le nom ne peut pas être vide.');
        }

        $picture = $this->entityManager
            ->getRepository(Picture::class)
            ->findOneByName($name)
        ;

        if (null === $picture) {
            throw new TransformationFailedException(sprintf(
                'An issue with number "%s" does not exist!', $name
            ));
        }

        $picture->setName($name);

        return $picture;
    }
}
