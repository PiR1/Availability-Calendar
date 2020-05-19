<?php


class User
{

    // table columns
    private $id;
    private $username;
    private $password;

    /**
     * user constructor.
     * @param $username
     * @param $password
     */
    public function __construct($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param integer $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return String
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param String $username
     */
    public function setUsername($username): void
    {
        $this->username = $username;
    }

    /**
     * @return String
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param String $password
     */
    public function setPassword($password): void
    {
        $this->password = $password;
    }


    /**
     * Transform user attributes to array
     *
     * @return array
     */
    public function toArray(){
        return array(
            "username" => $this->username,
            "password" => $this->password,
        );
    }
}