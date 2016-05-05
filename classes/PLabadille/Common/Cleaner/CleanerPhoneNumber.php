<?php
namespace PLabadille\Common\Cleaner;

class CleanerPhoneNumber implements CleanerInterface
{
    public function clean($value){
        if ( preg_match("#^[0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9]$#", $value) ){
            $numTel = preg_replace("#(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})#", "$1 $2 $3 $4 $5", $value);
            return $numTel;
        } else{
            return $value;
        }
    }
}