<?php

namespace App\Service;

use Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

Class AvatarService 
{
    private $folder;
    private $imgDirectory;
    private $width = 300;
    private $heigth = 300;


    public function __construct(ParameterBagInterface $params) {
        $this->folder = $params->get('avatar.picture.folder');
        $this->imgDirectory = $params->get('images_directory');
    }

    /**
     * Function to add an avatar with a uniqid
     */
    public function storeWithSafeName(UploadedFile $picture):string 
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

        switch($pictureDatas['mime']) {
            case 'image/png':
                $pictureSource = imagecreatefrompng($picture);
                break;
            case 'image/jpeg':
                $pictureSource = imagecreatefromjpeg($picture);
                break;  
            case 'image/webp':
                $pictureSource = imagecreatefromwebp($picture);
                break;
            default:
                throw new Exception('Format d\'image non valide. Format pris en charge: jpeg, png, webp.');
        }

        $imageWidth = $pictureDatas[0];
        $imageHeight = $pictureDatas[1];

        switch ($imageWidth <=> $imageHeight) {
            case -1: // portrait
                $squareSize = $imageWidth;
                $src_x = 0;
                $src_y = ($imageHeight - $squareSize) / 2;
                break;
            case 0: // carré
                $squareSize = $imageWidth;
                $src_x = 0;
                $src_y = 0;
                break;
            case 1: // paysage
                $squareSize = $imageWidth;
                $src_x = ($imageHeight - $squareSize) / 2;
                $src_y = 0;
                break;
        }

        $resizePicture = imagecreatetruecolor($this->width, $this->heigth);
        imagecopyresampled($resizePicture, $pictureSource, 0, 0, $src_x, $src_y, $this->width, $this->heigth, $squareSize, $squareSize);
        imagepng($resizePicture, $path.'/'.$this->width.'x'.$this->heigth.'-'.$field);

        return $field;
    }
}
