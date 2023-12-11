<?php

namespace App\Service;

use App\Entity\Picture;
use Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

Class TrickPictureService 
{
    private $folder;
    private $imgDirectory;
    

    public function __construct(ParameterBagInterface $params) {
        $this->folder = $params->get('trick.picture.folder');
        $this->imgDirectory = $params->get('images_directory');
    }

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
    public function replace(UploadedFile $picture, string $actualPictureName):string
    {
        $imageName = $this->storeWithSafeName($picture);
        $this->delete($actualPictureName);

        return $imageName;
    }

    /**
     * Function to delete a trick picture
     */
    public function delete(string $field):bool
    {
        $success = false;        

        $path = $this->imgDirectory.$this->folder;
        $original = $path.'/'.$field;

        if (file_exists($original)) {
            unlink($original);
            $success = true;
        }

        return $success;
    }
}
