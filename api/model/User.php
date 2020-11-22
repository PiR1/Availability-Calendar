<?php
/**
 * Copyright (C) PiR1, Inc - All Rights Reserved
 *    Apache License
 *    Version 2.0, January 2004
 *    http://www.apache.org/licenses/
 *    See Licence file
 *
 * @file      User.php
 * @author    PiR1
 * @date     25/05/2020 23:25
 */

namespace Calendar\model;

class User
{

    // table columns
    private $id;
    private $username;
    private $password;

    /**
     * User constructor.
     */
    public function __construct(){

    }

    /**
     * User creator.
     * @param $username
     * @param $password
     * @return User
     */
    public static function create($username, $password)
    {
        $self = new self();
        $self->username = $username;
        $self->password = $password;
        return $self;
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