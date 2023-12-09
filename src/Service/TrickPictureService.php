<?php

namespace App\Service;

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
     * Function to add a trick picture with a uniqid
     */
    public function add(UploadedFile $picture):string 
    {
        $path = $this->imgDirectory.$this->folder;
        if (!file_exists($path.'/')) {
            mkdir($path.'/', 0755, true);
        }

        $field = md5(uniqId(rand(), true)).'.png';
        $pictureDatas = getImageSize($picture);

        if ($pictureDatas === false) {
            throw new Exception('Format d\'image incorrect');
        }

        $picture->move($path.'/',$field);

        return $field;
    }

    public function replace()
    {

    }

    /**
     * Function to delete a trick picture
     */
    public function delete(string $field):bool
    {
        if ($field !== 'default-avatar.png') {
            $success = false;

            $path = $this->imgDirectory.$this->folder;
            $original = $path.'/'.$field;

            if (file_exists($original)) {
                unlink($original);
                $success = true;
            }

            return $success;
        }

        return false;
    }
}
