<?php

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use App\Entity\Media;

class MediaTransformer implements DataTransformerInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }
    public function reverseTransform($media): Media
    {
        if (!$media instanceof Media) {
            return throw new TransformationFailedException('Cela ne correspond a aucun lien enregistré.');
        }
        return $media;
    }

    public function transform($videoUrl): Media
    {
        $media = $this->entityManager
            ->getRepository(Media::class)
            ->findOneByVideoUrl($videoUrl)
        ;

        if (null === $media) {
            throw new TransformationFailedException(sprintf(
                'Un lien avec cet identifiant n\existe pas', $videoUrl
            ));
            $privateErrorMessage = sprintf('Un lien avec cet identifiant n\existe pas.', $videoUrl);
            $publicErrorMessage = 'La valeure "{{ value }}" n\'est pas un lien valide .';

            $failure = new TransformationFailedException($privateErrorMessage);
            $failure->setInvalidMessage($publicErrorMessage, [
                '{{ value }}' => $videoUrl,
            ]);

            throw $failure;
        }

        $media->setVideoUrl($videoUrl);

        return $media;
    }

    // /**
    //  * Transforms an object (Media) to a string (videoUrl).
    //  * 
    //  * @param  Media|null $media
    //  */
    // public function reverseTransform($media): string
    // {
    //     dump('transform', $media);
    //     if (!$media instanceof Media) {
    //         return throw new TransformationFailedException('Cela ne correspond a aucun lien enregistré.');
    //     }

    //     return $media->getVideoUrl();
    // }

    // /**
    //  * Transforms a string (videoUrl) to an object (Media).
    //  *
    //  * @param  string $videoUrl
    //  * @throws TransformationFailedException if object (Media) is not found.
    //  */
    // public function transform($videoUrl): ?Media
    // {
    //     if (!$videoUrl) {
    //         return throw new TransformationFailedException('Le champs ne peut pas être vide.');
    //     }

    //     $media = $this->entityManager
    //         ->getRepository(Media::class)
    //         ->findOneByVideoUrl($videoUrl)
    //     ;

    //     if (null === $media) {
    //         throw new TransformationFailedException(sprintf(
    //             'Un lien avec cet identifiant n\existe pas', $videoUrl
    //         ));
    //         $privateErrorMessage = sprintf('Un lien avec cet identifiant n\existe pas.', $videoUrl);
    //         $publicErrorMessage = 'La valeure "{{ value }}" n\'est pas un lien valide .';

    //         $failure = new TransformationFailedException($privateErrorMessage);
    //         $failure->setInvalidMessage($publicErrorMessage, [
    //             '{{ value }}' => $videoUrl,
    //         ]);

    //         throw $failure;
    //     }

    //     $media->setVideoUrl($videoUrl);

    //     return $media;
    // }
}
