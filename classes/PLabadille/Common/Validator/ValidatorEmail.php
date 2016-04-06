<?php
namespace PLabadille\Common\Validator;

class ValidatorEmail implements ValidatorInterface
{
    public function validate($value){
        //retourne false si erreure.
        return (preg_match("#^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$#", $value)) ? null : "<br>*Adresse email non valide";
    }
} 