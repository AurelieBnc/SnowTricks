<?php

namespace App\Manager;

use App\Entity\Picture;
use App\Entity\Trick;
use App\Service\TrickPictureService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PictureManager 
{
    private $entityManager;
    private $trickPictureService;

    public function __construct(
        EntityManagerInterface $entityManager,
        TrickPictureService $trickPictureService,
    ) {
        $this->entityManager = $entityManager;
        $this->trickPictureService = $trickPictureService;
    }

    public function addUploadedPictureFile(UploadedFile $pictureUploaded, $trick): void
    {
        $newNamePicture = $this->trickPictureService->storeWithSafeName($pictureUploaded);
        $picture = new Picture;

        $picture->setName($newNamePicture);

        $trick->addPicture($picture);
        $this->entityManager->persist($trick);

        $trick->setHeaderImage(
            $trick->getPictureList()[0]?->getName() ? $trick->getPictureList()[0]->getName() : null
        );

        // $this-> entityManager->flush();
    }

    public function addUploadedPicturesFile(array $pictureUploadedFileList, $trick): void
    {
        foreach ($pictureUploadedFileList as $pictureUploaded) {
            $this->addUploadedPictureFile($pictureUploaded, $trick);
        }
    }
}