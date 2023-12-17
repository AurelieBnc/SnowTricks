<?php

namespace App\Service;

use App\Entity\Picture;
use Exception;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\UploadedFile;

Class TrickPictureFileService 
{
    public function __construct(
        #[Autowire('%trick.picture.folder%')]
        private string $folder,
        #[Autowire('%images_directory%')]
        private string $imgDirectory,
    ) {}

    /**
     * Function to create a new trick picture name with a uniqid
     */
    public function storeWithSafeName(UploadedFile $picture):string 
    {
        $path = $this->imgDirectory.$this->folder;
        if (!file_exists($path.'/')) {
            mkdir($path.'/', 0755, true);
        }

        $imageName = md5(uniqId(rand(), true)).'.png';
        $pictureDatas = getImageSize($picture);

        if ($pictureDatas === false) {
            throw new Exception('Format d\'image incorrect');
        }

        $picture->move($path.'/',$imageName);

        return $imageName;
    }

    /**
     * Function to replace a trick picture
     */
    public function replace(UploadedFile $pictureUploadedFile, Picture $picture):string
    {
        $imageName = $this->storeWithSafeName($pictureUploadedFile);
        $this->delete($picture);

        return $imageName;
    }

    /**
     * Function to delete a trick picture
     */
    public function delete(Picture $picture):bool
    {
        $pictureName = $picture->getName();
        $picturePath = $this->imgDirectory . $this->folder . DIRECTORY_SEPARATOR . $pictureName;
        
        return  file_exists( $picturePath) && unlink($picturePath);
    }
}
