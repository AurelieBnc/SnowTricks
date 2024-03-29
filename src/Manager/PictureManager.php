<?php

namespace App\Manager;

use App\Entity\Picture;
use App\Entity\Trick;
use App\Service\TrickPictureFileService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PictureManager 
{
    private $entityManager;
    private $trickPictureFileService;

    public function __construct(
        EntityManagerInterface $entityManager,
        TrickPictureFileService $trickPictureFileService,
    ) {
        $this->entityManager = $entityManager;
        $this->trickPictureFileService = $trickPictureFileService;
    }

    public function addUploadedPictureFile(UploadedFile $pictureUploaded,Trick $trick): void
    {
        $newPictureName = $this->trickPictureFileService->storeWithSafeName($pictureUploaded);
        $picture = new Picture;

        $picture->setName($newPictureName);

        $trick->addPicture($picture);
        $this->entityManager->persist($trick);

        $trick->setHeaderPictureName(
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
        $newPictureName = $this->trickPictureFileService->replace($uploadedPictureFile, $picture);

        if ($pictureName === $trick->getHeaderPictureName()) {
            $trick->setHeaderPictureName($newPictureName);
            $this->entityManager->persist($trick);
        }
        
        $picture->setName($newPictureName); 
        $picture->setTrick($trick);

        $this->entityManager->persist($picture);
    }

    public function deletePictureAndPictureFile(Picture $deletePicture, Trick $trick): void
    {
        $this->entityManager->remove($deletePicture);

        $isHeaderPicture = $deletePicture->getName() === $trick->getHeaderPictureName();

        if ($isHeaderPicture) {
            $pictureList = $trick->getPictureList();
            $trick->setHeaderPictureName($pictureList[1]?->getName());
        }

        $this->entityManager->persist($trick);
        $this->entityManager->flush();

        $this->trickPictureFileService->delete($deletePicture);
    }

    public function deletePictureFileList(Trick $trick):void
    {
        $pictureList = $trick->getPictureList();

        foreach ($pictureList as $deletePicture) {
            $this->trickPictureFileService->delete($deletePicture);
        }
    }
}
