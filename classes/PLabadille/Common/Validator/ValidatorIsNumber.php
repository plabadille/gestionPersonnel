<?php
namespace PLabadille\Common\Validator;

class ValidatorIsNumber implements ValidatorInterface
{
    public function validate($value){

        return (is_numeric($value)) ? null : "<br>*Vous devez saisir un chiffre";
    }
} 