<?php

namespace PLabadille\Common\Controller;

class Request
{
    protected $get;
    protected $post;
    protected $files;
    protected $session;

    public function __construct()
    {
        $this->get = $_GET;
        $this->post = $_POST;
        $this->files = $_FILES;
        $this->session = $_SESSION;
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

    public function getSessionAttribute($key)
    {
        if (isset($this->session[$key])){
            return $this->session[$key];
        } else {
            return null;
        }
    }
    public function setSession($key, $value)
    {
        $this->session[$key] = $value;
        $_SESSION[$key] = $value;
    }

}