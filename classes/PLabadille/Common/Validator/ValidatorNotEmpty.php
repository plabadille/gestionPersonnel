<?php
namespace PLabadille\Common\Validator;

class ValidatorNotEmpty implements ValidatorInterface
{
    public function validate($value){
        //retourne false si erreure.
        return (!empty($value)) ? null : "<br>*le champ est vide";
    }
} 