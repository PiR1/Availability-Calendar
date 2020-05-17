<?php


abstract class controller
{
    protected $connection;
    protected $tableName;

    public function __construct($connection, $tableName)
    {
        $this->connection = $connection;
        $this->tableName = $tableName;
        $this->isTable($tableName);
    }

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

    private function createTable()
    {
        $query = "CREATE TABLE IF NOT EXISTS `events` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `start` date NOT NULL,
                  `end` date NOT NULL,
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
    }


    /**
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
     * @param $id
     * @return mixed
     */

    protected function remove($id){
        $query = "Delete from " . $this->tableName . " WHERE id=:id";
        // prepare query statement

        $stmt = $this->connection->prepare($query);

        // bind id of product to be updated
        $stmt->bindValue(":id", $this->prepareValue($id));


        // execute query
        return $stmt->execute();
    }



    /**
     * @param $value
     * @return string
     */
    protected function prepareValue($value)
    {
        return htmlspecialchars(strip_tags($value));
    }

}