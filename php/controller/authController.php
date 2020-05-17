<?php


class authController
{
    public function __construct()
    {
    }

    /**
     * @return bool
     */
    public function checkAuth(){
//        session_start();
        if(!isset($_SESSION)) { session_start(); }
        if(session_id() == '') {
            session_start();
        }
        if(isset($_SESSION["username"]) && !empty($_SESSION["username"])){
            return true;
        }
        else{
            return false;
        }
    }

    /**
     * @return integer|null
     */
    public function getAuth(){
        if ($this->checkAuth()){
            return $_SESSION["username"];
        }
        else{
            return null;
        }
    }

    /**
     * @param User $user
     */

    public function setAuth($user){
        if(!isset($_SESSION)) { session_start(); }
        $_SESSION["username"] = $user->getUsername();
    }

    public function deleteAuth(){
        if(!isset($_SESSION)) { session_start(); }
        $_SESSION["username"]="";
        session_destroy();
        echo json_encode(array("message" => "Logout successfully"));
    }

}