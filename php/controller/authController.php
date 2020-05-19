<?php


class authController
{
    public function __construct()
    {
    }

    /**
     * Check if the user is auth
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
     * Get username of the auth user
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
     * Create the session
     * @param User $user
     */

    public function setAuth($user){
        if(!isset($_SESSION)) { session_start(); }
        $_SESSION["username"] = $user->getUsername();
    }

    /**
     * Delete the session
     */
    public function deleteAuth(){
        if(!isset($_SESSION)) { session_start(); }
        $_SESSION["username"]="";
        session_destroy();
        echo json_encode(array("message" => "Logout successfully"));
    }

}