<?php

namespace PLabadille\Common\Bd;
use \PDO;
require_once 'config/mysql_config.php';

class DB
{
    static protected $instance = null;
    protected $pdo;

    private function __construct() {
        $this->pdo = new PDO(
            'mysql:host='.HOST.';port='.PORT.';dbname='.DATABASE.';charset=utf8',
            USER,
            PASSWORD
        );
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }

    public function __clone() {}

    static public function getInstance() {
        if (self::$instance === null)
            self::$instance = new self();
        return self::$instance;
    }

    public function getPDO() {
        return $this->pdo;
    }
}

//$pdo = DB::getInstance()->getPDO();