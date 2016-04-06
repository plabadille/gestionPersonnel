<?php
namespace PLabadille\Common\Cleaner;

class CleanerHtmlTags implements CleanerInterface
{
    public function clean($value){
        return strip_tags($value);
    }
}