<?php

namespace PLabadille\Common\Bd;
use \PDO;
require_once 'config/mysql_config.php';

class DB
{
    static protected $instance = null;
    protected $pdo;

    private function __construct()
    {
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

    public function getDump()
    {
        $dbuser=USER;
        $dbpasswd=PASSWORD;
        $host=HOST;
        $database=DATABASE;

        $filename = "dump-" . date("d-m-Y") . ".sql.gz";
        $mime = "application/x-gzip";

        header( "Content-Type: " . $mime );
        header( 'Content-Disposition: attachment; filename="' . $filename . '"' );

        $cmd = "mysqldump --opt --host=$host --user=$dbuser --password=$dbpasswd $database | gzip --best"; 

        passthru( $cmd );

        exit(0);
    }

    public function internalDump()
    {
        $dbuser=USER;
        $dbpasswd=PASSWORD;
        $host=HOST;
        $database=DATABASE;

        $path='data/autoDump/';

        $filename = "internaldump-" . date("d-m-Y") . ".sql";

        system("mysqldump --host=".$host." --user=".$dbuser." --password=".$dbpasswd." ".$database."  > ".$path.$filename);
    }

    public function getPDO()
    {
        return $this->pdo;
    }
}

//$pdo = DB::getInstance()->getPDO();