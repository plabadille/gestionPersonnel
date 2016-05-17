<?php
namespace PLabadille\Common\Validator;

class ValidatorIsString implements ValidatorInterface
{
    public function validate($value)
    {
        return (preg_match("#^[^0-9][a-z, A-Z, éèëê\-]*[^0-9][a-z, A-Z, éèëê\-]*[^0-9]$#", $value)) ? null : "<br>*Vous devez saisir des lettres, les chiffres ou caractères spéciaux ne sont pas autorisé (caractères autorisés : éèëê-)";
    }
} 
