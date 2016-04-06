<?php

namespace PLabadille\Common\Controller;

class Response
{
    protected $parts;

    public function __construct()
    {
        $this->parts=[];
    }

    public function setPart($key, $value)
    {
        $this->parts[$key]=$value;
    }

    public function getPart($key)
    {
        if (isset($this->parts[$key])){
            return $this->parts[$key];
        } else{
            die('Paramètre ' . $key . ' inconnu');
        }
    }
}