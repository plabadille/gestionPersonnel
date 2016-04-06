<?php
namespace PLabadille\Common\Validator;

class ValidatorAgeBetween implements ValidatorInterface
{
    public function validate($value){
        //retourne false si erreure.
        return (preg_match("#^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$#", $value)) ? null : "<br>*Email non valide";
    }
} 