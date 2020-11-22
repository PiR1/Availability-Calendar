<?php
/**
 * Copyright (C) PiR1, Inc - All Rights Reserved
 *    Apache License
 *    Version 2.0, January 2004
 *    http://www.apache.org/licenses/
 *    See Licence file
 *
 * @file      index.php
 * @author    PiR1
 * @date     25/05/2020 23:25
 */

namespace Calendar;
require __DIR__.'/Autoloader.php';
use Calendar\Autoloader;
Autoloader::register();
// Set the header
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// get database connection
use Calendar\config\DBClass;
// get objects classes
use Calendar\controller\eventController;
use Calendar\controller\icsController;
use Calendar\controller\userController;
use Calendar\controller\authController;
use Exception;


// get request uri
if (!isset($_SESSION)) {
    session_start();
}
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', $uri);
$_SESSION["path"] = implode("/", array_slice($uri, 0, array_search("php", $uri))) . "/";
// $uri = array_slice($uri, array_search("php",$uri)+1);
$uri = array_slice($uri, array_search("Availability-Calendar", $uri));
// decode ths json data
$data = json_decode(file_get_contents("php://input"));
$requestMethod = $_SERVER["REQUEST_METHOD"];
// var_dump($uri);

$auth = new authController();

try {
    if (isset($uri[2])) {
        switch ($uri[2]) {
            case 'event':
                if (isset($uri[3])) {
                    $database = new DBClass();
                    $db = $database->getConnection();
                    $event = new eventController($db);
                    switch ($uri[3]) {
                        case 'create':
                            if ($auth->checkAuth()) {
                                $msg = $event->create($data);
                                echo json_encode(array("message" => $msg));
                            } else {
                                throw new Exception("Unauthorized.", 401);
                            }

                            break;

                        case 'getAll':
                            $event->getAll();
                            break;

                        case "changeState":
                            if ($auth->checkAuth()) {
                                $event->updateDateState($data);
                            } else {
                                throw new Exception("Unauthorized.", 401);
                            }

                            break;

                        default:
                            throw new Exception("Unsupported request.", 501);
                            break;
                    }
                } else {
                    throw new Exception("Bad request.", 501);
                }
                break;
            case 'user':
                if (isset($uri[3])) {
                    $database = new DBClass();
                    $db = $database->getConnection();
                    $user = new userController($db);
                    switch ($uri[3]) {
                        case 'login':
                            $user->login($data);
                            break;

                        case 'signup':
                            if ($auth->checkAuth()) {
                                $user->signUp($data);
                            } else {
                                throw new Exception("Unauthorized.", 401);
                            }
                            break;

                        case 'password':
                            if ($auth->checkAuth()) {
                                $user->changePassword($data);
                            } else {
                                throw new Exception("Unauthorized.", 401);
                            }
                            break;
                        case 'logout':
                            $auth->deleteAuth();
                            break;

                        default:
                            throw new Exception("Unsupported request.", 501);
                            break;
                    }
                } else {
                    throw new Exception("Bad request.", 501);
                }
                break;

            case 'admin':
                if (isset($uri[3])) {
                    $database = new DBClass();
                    $db = $database->getConnection();
                    $user = new userController($db);
                    switch ($uri[3]) {
                        case 'login':
                            return "admin/login.php";
                            break;

                        case 'signup':
                            $user->signUp($data);
                            break;

                        default:
                            throw new Exception("Unsupported request.", 501);
                            break;
                    }
                } else {
                    throw new Exception("Bad request.", 501);
                }
                break;

            case 'ical':
                if (isset($uri[3])) {
                    $database = new DBClass();
                    $db = $database->getConnection();
                    $icalCtrl = new icsController($db);
                    switch ($uri[3]) {
                        case 'Calendar.ics':
                            $icalCtrl->mysqlToIcs();
                            break;
                        case 'getAll':
                            if ($auth->checkAuth()) {
                                $icalCtrl->getAll();
                            } else {
                                throw new Exception("Unauthorized.", 401);
                            }
                            break;

                        case 'update':
                            if ($auth->checkAuth()) {
                                $icalCtrl->updateIcal($data);
                            } else {
                                throw new Exception("Unauthorized.", 401);
                            }
                            break;

                        case 'updateCal':
                                $icalCtrl->icsToMysql();
                            break;

                        case 'delete':
                            if ($auth->checkAuth()) {
                                $icalCtrl->delete($data);

                            } else {
                                throw new Exception("Unauthorized.", 401);
                            }
                            break;

                        default:
                            throw new Exception("Unsupported request.", 501);
                            break;
                    }
                } else {
                    throw new Exception("Bad request.", 501);
                }
                break;

            default:
                throw new Exception("Unsupported request.", 501);
                break;
        }
    } else {
        throw new Exception("Unsupported request.", 501);
    }

} catch (Exception $e) {
    // send error
    http_response_code($e->getCode());
    echo json_encode(array("message" => $e->getMessage()));
}
