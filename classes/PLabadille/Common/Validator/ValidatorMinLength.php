<?php
namespace PLabadille\Common\Validator;

class ValidatorMinLength implements ValidatorInterface
{
    public function validate($value){
        //retourne false si erreure.
        $min = 10;
        return (strlen($value)>$min) ? null : '<br>*Le nombre de caractère minimum est de ' . $min;
    }
} 