<?php
namespace PLabadille\Common\Validator;

class ValidatorDateFormat implements ValidatorInterface
{
    public function validate($value){
        #year date['0'] month date['1'] day['2']
        $date = explode('-', $value);

        return (checkdate($date['1'], $date['2'], $date['0'])) ? null : "<br>*format date non valide : exemple 2012-10-25";
    }
} 