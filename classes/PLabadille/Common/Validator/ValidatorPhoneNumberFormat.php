<?php
namespace PLabadille\Common\Validator;

class ValidatorPhoneNumberFormat implements ValidatorInterface
{
    public function validate($value){
        //retourne false si erreure.
        return (preg_match("#^[0][1-9]\s[0-9][0-9]\s[0-9][0-9]\s[0-9][0-9]\s[0-9][0-9]$#", $value)) ? null : "<br>*format ou numéro incorect, ex: 01 22 53 58 11";
    }
} 