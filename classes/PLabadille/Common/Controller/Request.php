<?php

namespace PLabadille\Common\Controller;

class Request
{
    protected $get;
    protected $post;
    protected $files;

    public function __construct()
    {
        $this->get = $_GET;
        $this->post = $_POST;
        $this->files = $_FILES;
    }

    public function getGetAttribute($key)
    {
        if (isset($this->get[$key])){
            return $this->get[$key];
        } else{
            return null;
//            die('Paramètre ' . $key . ' inconnu');
        }
    }
    public function getPostAttribute($key)
    {
        if (isset($this->post[$key])){
            return $this->post[$key];
        } else{
            return null;
            //die('Paramètre ' . $key . ' inconnu');
        }
    }
    public function getFilesAttributes($key)
    {
        if (isset($this->files[$key])){
            return $this->files[$key];
        } else{
            return null;
        }
    }
    public function getPost()
    {
        return $this->post;
    }
    public function getGet()
    {
        return $this->get;
    }
    public function getFiles()
    {
        return $this->files;
    }
}