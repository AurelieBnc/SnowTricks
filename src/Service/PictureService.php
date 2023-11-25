<?php

namespace App\Service;

use Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

Class PictureService 
{
    private $params;

    public function __construct(ParameterBagInterface $params) {
        $this->params = $params;
    }

    /**
     * Function to add a picture with a uniqid
     */
    public function add(UploadedFile $picture, ?string $folder = '', ?int $width = 250, ?int $heigth = 250):string 
    {
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

        $resizePicture = imagecreatetruecolor($width, $heigth);

        imagecopyresampled($resizePicture, $pictureSource, 0, 0, $src_x, $src_y, $width, $heigth, $squareSize, $squareSize);

        $path = $this->params->get('images_directory').$folder;

        if (!file_exists(($path.'/mini/'))) {
            mkdir($path.'/mini/', 0755, true);
        }

        imagepng($resizePicture, $path.'/mini/'.$width.'x'.$heigth.'-'.$field);
        $picture->move($path.'/',$field);

        return $field;

    }

    public function delete(string $field, ?string $folder = '', ?int $width = 250, ?int $heigth =250):bool
    {
        if ($field !== 'default-avatar.png') {
            $success = false;
            $path = $this->params->get('images_directory').$folder;

            $mini = $path.'/mini/'.$width.'x'.$heigth.'-'.$field;

            if (file_exists($mini)) {
                unlink($mini);
                $success =true;
            }

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
