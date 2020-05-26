<?php
/**
 * Copyright (C) PiR1, Inc - All Rights Reserved
 *    Apache License
 *    Version 2.0, January 2004
 *    http://www.apache.org/licenses/
 *    See Licence file
 *
 * @file      dbclass.php
 * @author    PiR1
 * @date     25/05/2020 23:25
 */

namespace Calendar\config;

use \PDO;
use Exception;
use PDOException;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
class DBClass {

    private $host = "localhost";
    private $username = "root";
    private $password = "";
    private $database = "calendar";
    private $port = 3308;


    public $connection;

    // get the database connection
    public function getConnection(){

        $this->connection = null;

        try{
            $this->connection = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->database.';port='.$this->port.';', $this->username, $this->password);
            $this->connection->exec("set names utf8");
        }catch(PDOException $exception){
            echo "Error: " . $exception->getMessage();
            throw new Exception("Impossible de se connecter à la base de données", 500);
            
        }
        return $this->connection;
    }
}
