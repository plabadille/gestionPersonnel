<?php
namespace PLabadille\Common\Cleaner;

class CleanerTrim implements CleanerInterface
{
    public function clean($value){
        return trim($value);
    }
}