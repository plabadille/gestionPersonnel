<?php
namespace PLabadille\Common\Loader;

class AutoLoader
{
    public static function load($class){
        $path = str_replace('\\', DIRECTORY_SEPARATOR, $class);
        include 'classes/' . $path . '.php';
    }
}