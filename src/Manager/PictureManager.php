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

    public function addUploadedPictureFile(UploadedFile $pictureUploaded,Trick $trick): void
    {
        $newNamePicture = $this->trickPictureService->storeWithSafeName($pictureUploaded);
        $picture = new Picture;

        $picture->setName($newNamePicture);

        $trick->addPicture($picture);
        $this->entityManager->persist($trick);

        $trick->setHeaderImage(
            $trick->getPictureList()[0]?->getName() ? $trick->getPictureList()[0]->getName() : null
        );
    }

    public function addUploadedPictureFileList(array $pictureUploadedFileList,Trick $trick): void
    {
        foreach ($pictureUploadedFileList as $pictureUploaded) {
            $this->addUploadedPictureFile($pictureUploaded, $trick);
        }
    }

    public function editUploadedPictureFile(Picture $picture, UploadedFile $uploadedPictureFile, Trick $trick): void
    {
        $pictureName = $picture->getName();
        $newNamePicture = $this->trickPictureService->replace($uploadedPictureFile, $pictureName);

        if ($pictureName === $trick->getHeaderImage()) {
            $trick->setHeaderImage($newNamePicture);
            $this->entityManager->persist($trick);
        }
        
        $picture->setName($newNamePicture); 
        $picture->setTrick($trick);

        $this->entityManager->persist($picture);
    }

    public function deletePictureAndPictureFile(Picture $deletePicture, Trick $trick): void
    {
        $this->entityManager->remove($deletePicture);

        if ($deletePicture->getName() === $trick->getHeaderImage()) {
            $trick->setHeaderImage($trick->getPictureList()[1]?->getName() ? $trick->getPictureList()[1]->getName() : null);
        }

        $this->entityManager->persist($trick);
        $this->entityManager->flush();

        $this->trickPictureService->delete($deletePicture->getName());
    }

    public function deletePictureFileList(Trick $trick):void
    {
        $pictureList = $trick->getPictureList();

        foreach ($pictureList as $deletePicture) {
            $this->trickPictureService->delete($deletePicture->getName());
        }
    }
}
