<?php
namespace PLabadille\Common\Validator;

class ValidatorDateFormat implements ValidatorInterface
{
    public function validate($value){
        #year date['0'] month date['1'] day['2']
        $date = explode('-', $value);
        if ( !isset($date['0']) || !isset($date['1']) || !isset($date['2'])){
            return "<br>*format date non valide : exemple 2012-10-25";
        } else{
            return (checkdate($date['1'], $date['2'], $date['0'])) ? null : "<br>*format date non valide : exemple 2012-10-25";
        }
    }
} 