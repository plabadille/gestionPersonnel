<?php
namespace PLabadille\Common\Validator;

class ValidatorDateFormat implements ValidatorInterface
{
    public function validate($value){
        #Regexp : format->yyyy-mm-dd ; y : doit commencer par 1 ou 2 ->1000à2999 ; m : de 0 à 9 et de 1 à 12; d : même principe 
        return (preg_match("#^[1-2][0-9][0-9][0-9][-]([0][1-9])|([1][0-2])[-]([0-2][0-9])|([3][0-1])$#", $value)) ? null : "<br>*format date non valide : exemple 2012-10-25";
    }
} 