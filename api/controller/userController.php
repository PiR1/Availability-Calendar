<?php
/**
 * Copyright (C) PiR1, Inc - All Rights Reserved
 *    Apache License
 *    Version 2.0, January 2004
 *    http://www.apache.org/licenses/
 *    See Licence file
 *
 * @file      userController.php
 * @author    PiR1
 * @date     25/05/2020 23:25
 */

namespace Calendar\controller;

use Exception;
use Calendar\model\User;

class userController extends controller
{

    public function __construct($connection)
    {
        parent::__construct($connection, "users", "\Calendar\model\User");
    }

    /**
     * Login
     * @param $data
     * @throws Exception
     */
    public function login($data)
    {
        if (!empty($data->username) &&
            !empty($data->password)) {
            $query = "Select * from " . $this->tableName . "
                        Where username=:username";

            $stmt = $this->connection->prepare($query);

            // bind values
            $stmt->bindParam(":username", $data->username);

            // execute query
            if ($stmt->execute()) {
                $usr = $this->toModel($stmt);
                if (password_verify($data->password, $usr->getPassword())) {
                    (new authController)->setAuth($usr);
                    http_response_code(201);
                    // tell the user
                    echo json_encode(array("message" => "Logged in"));
                } else {
                    throw new Exception("Login failed", 401);
                }
            } else {
                throw new Exception("Login failed", 401);
            }
        } else {
            throw new Exception("Unable to log user. Data is incomplete.", 400);
        }
    }

    /**
     * Sign Up
     * @param $data
     * @throws Exception
     */

    public function signUp($data)
    {
        if (!empty($data->username) &&
            !empty($data->password)) {

            $query = "INSERT INTO " . $this->tableName . "
                        SET username=:username, password=:password";

            // prepare query
            $stmt = $this->connection->prepare($query);

            // bind values
            $stmt->bindValue(":username", htmlspecialchars(strip_tags($data->username)));
            $stmt->bindValue(":password", password_hash(htmlspecialchars(strip_tags($data->password)), PASSWORD_DEFAULT));

            // execute query
            if ($stmt->execute()) {
                // set response code - 201 Created
                http_response_code(201);
                // tell the user
                echo json_encode(array("message" => "User was created."));
            } // if unable to create the user, tell the user
            else {
                // set response code - 503 service unavailable
                throw new Exception("Unable to create user", 503);
            }
        } else {
            throw new Exception("Unable to create user. Data is incomplete.", 400);
        }
    }

    /**
     * Change password
     * @param $data
     * @throws Exception
     */

    public function changePassword($data)
    {
        if (!empty($data->oldPassword) &&
            !empty($data->password) &&
            !empty($data->newPassword)) {
            if ($data->password == $data->newPassword) {

                $user= (new authController)->getAuth();

                $stmt = $this->getByUsername($user);
                if ($stmt->execute()) {
                    /** @var User $usr */
                    $usr=$this->toModel($stmt);
                    if ($usr->getUsername()==$user){
                        if (password_verify($data->oldPassword,$usr->getPassword())) {
                            if ($this->update($usr->getId(), "password", password_hash($data->password, PASSWORD_DEFAULT))) {
                                http_response_code(200); //Success
                                echo json_encode(array("message" => "Password changed"));
                            } else {
                                throw new Exception("Internal error", 500);
                            }
                        } else {
                            throw new Exception("Wrong password", 409);
                        }
                    }else{
                        throw new Exception("Forbidden", 403);
                    }
                } else {
                    throw new Exception("Internal error", 500);
                }
            } else {
                throw new Exception("Passwords don't match", 409);
            }
        } else {
            throw new Exception("Unable to change password. Data is incomplete.", 400);
        }


    }

    /**
     * Get user by username
     * @param $username
     * @return mixed
     */

    private function getByUsername($username)
    {
        $query = "Select * from " . $this->tableName . " Where username=:username";

        $stmt = $this->connection->prepare($query);

        // bind values
        $stmt->bindParam(":username", $username);

        return $stmt;
    }
}