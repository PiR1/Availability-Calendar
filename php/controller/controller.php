<?php
/**
 * Copyright (C) PiR1, Inc - All Rights Reserved
 *    Apache License
 *    Version 2.0, January 2004
 *    http://www.apache.org/licenses/
 *    See Licence file
 *
 * @file      controller.php
 * @author    PiR1
 * @date     25/05/2020 23:25
 */

namespace Calendar\controller;

use \PDO;
use Exception;

abstract class controller
{
    protected $connection;
    protected $tableName;
    protected $model;

    /**
     * controller constructor.
     * @param $connection
     * @param $tableName
     * @param $model
     * @throws Exception
     */
    public function __construct($connection, $tableName, $model)
    {
        $this->connection = $connection;
        $this->tableName = $tableName;
        $this->isTable($tableName);
        $this->model=$model;
    }

    /**
     * Check if $tbl exist in the database
     * @param $tbl
     * @throws Exception
     */
    private function isTable($tbl)
    {
        $tables = array();
        $query = "SHOW TABLES";
        $stmt = $this->connection->prepare($query);
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $tables[] = $row[0];
        }
        if (!in_array($tbl, $tables)){
            $this->createTable();
        }
    }

    /**
     * Create tables
     * @throws Exception
     */
    private function createTable()
    {
        $query = "CREATE TABLE IF NOT EXISTS `events` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `start` date NOT NULL,
                  `end` date NOT NULL,
                  `uid` varchar(255) NOT NULL, 
                  `idCal` int(11),
                  `description` varchar(255) NOT NULL,
                  PRIMARY KEY (`id`)
                )";
        $stmt = $this->connection->prepare($query);
        if (!$stmt->execute()) {
            throw new Exception("Internal error", 500);
        }

        $query = "CREATE TABLE IF NOT EXISTS `users` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `username` varchar(255) NOT NULL,
                  `password` varchar(255) NOT NULL,
                  PRIMARY KEY (`id`)
                );
                INSERT INTO `users` (`id`, `username`, `password`) VALUES
                (1, 'admin', '$2y$10$/mcbKecFsl0cOhrI6csR3O1sGAlDItR/6nK0XuJHokU268kWsR6le');";
        $stmt = $this->connection->prepare($query);
        if (!$stmt->execute()) {
            throw new Exception("Internal error", 500);
        }

        $query = "CREATE TABLE IF NOT EXISTS `ics` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `url` varchar(255) NOT NULL,
                  `type` varchar(255) NOT NULL,
                  PRIMARY KEY (`id`)
                );";
        $stmt = $this->connection->prepare($query);
        if (!$stmt->execute()) {
            throw new Exception("Internal error", 500);
        }
    }


    /**
     * Update an element in the db
     * @param integer $id
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    protected function update($id, $key, $value)
    {
        $query = "UPDATE " . $this->tableName . " SET " . $key . "=:value WHERE id=:id";
        // prepare query statement

        $stmt = $this->connection->prepare($query);

        // bind id of product to be updated
        $stmt->bindValue(":value", $this->prepareValue($value));
        $stmt->bindValue(":id", $this->prepareValue($id));

        // execute query
        return $stmt->execute();
    }

    /**
     * Remove an element in the db
     * @param $id
     * @return mixed
     */

    public function remove($id){
        $query = "Delete from " . $this->tableName . " WHERE id=:id";
        // prepare query statement

        $stmt = $this->connection->prepare($query);

        // bind id of product to be updated
        $stmt->bindValue(":id", $this->prepareValue($id));


        // execute query
        return $stmt->execute();
    }



    /**
     * Prepare $value to be replaced in a query
     * @param $value
     * @return string
     */
    protected function prepareValue($value)
    {
        return htmlspecialchars(strip_tags($value));
    }

    /**
     * Select all from db and generate a Model[]
     * @return mixed
     * @throws Exception
     */
    protected function fetchAll(){
        $query = "SELECT * FROM " . $this->tableName;

        // prepare query statement
        $stmt = $this->connection->prepare($query);

        // execute query
        $stmt->execute();

        $num = $stmt->rowCount();

        // check if more than 0 record found
        if ($num > 0) {
            return $stmt->fetchAll(PDO::FETCH_CLASS,$this->model);
        } else {
            throw new Exception("Nothing found", 404);
        }
    }

    /**
     * Generate a model from the query result
     * @param $stmt
     * @return mixed
     */
    protected function toModel($stmt){
        $stmt->setFetchMode(PDO::FETCH_CLASS,$this->model);
        return $stmt->fetch();
    }

}