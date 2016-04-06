<?php
namespace PLabadille\Common\Validator;

class ValidatorImageExtension implements ValidatorInterface
{
    public function validate($value){
        switch (getimagesize($value)[2]) {
            case IMG_JPG:
                return null;
                break;
            case IMG_PNG:
                return null;
                break;
            case IMG_GIF:
                return null;
                break;
            default:
               return 'Ce fichier n\'est pas une image ou le format n\'est pas accepté : .jpg, .png, .gif';
        }
    }
} 