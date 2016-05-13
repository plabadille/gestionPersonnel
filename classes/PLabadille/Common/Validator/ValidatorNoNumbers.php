<?php
namespace PLabadille\Common\Validator;

class ValidatorNoNumbers implements ValidatorInterface
{
    public function validate($value){
        //si il y a un chiffre ou plus dans la chaine le preg est true et on retourne l'erreur, sinon rien
        return (preg_match("#[0-9]#", $value)) ? "<br>*Ce champ ne doit pas contenir de chiffre" : null;
    }
} 
