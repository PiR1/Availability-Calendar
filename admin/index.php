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

require __DIR__.'/../php/Autoloader.php';
use Calendar\Autoloader;
Autoloader::register();
use Calendar\controller\authController;
if (!(new authController())->checkAuth()){
    header ('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Calendar admin</title>

    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        [role=button] {
            cursor: pointer;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="#">Calendar manager</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">

        </ul>
        <ul class="navbar-nav">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="userName" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <?=$_SESSION["username"]?>
                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userName">
                    <a class="dropdown-item" id="password" href="password.php">Change password</a>
                    <a class="dropdown-item" id="logout" href="#">Log out</a>
                </div>
            </li>
        </ul>
    </div>
</nav>
<div id="alerts" class="fixed-top mt-4 px-4 container"></div>
<div class="container">
    <div class="row">
        <div class="col-lg-6 col-sm-12 col-md-12"><div class="calendar py-4"></div></div>
        <div class="col-lg-1 col-sm-1 col-md-1"></div>
        <div class="col-lg-5 col-sm-12 col-md-12">
            <h3 class="text-center">Icals links</h3>
            <button type="button" id="addIcal" class="btn btn-primary"><i class="fa fa-plus" aria-hidden="true"></i></button>
            <div id="icals"></div>
        </div>
</div>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"
        integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0="
        crossorigin="anonymous"></script>
<script>var url_ajax_event = '<?=$_SESSION["path"]?>';</script>
<!--<button name="jump" onclick="jump()">Go</button>-->
<script src="../assets/js/dateParse.js"></script>
<script src="../assets/js/admin.js"></script>
<script src="../assets/js/script.js"></script>

<!-- Optional JavaScript for bootstrap -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"
        integrity="sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ"
        crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"
        integrity="sha384-uefMccjFJAIv6A+rW+L4AHf99KvxDjWSu1z9VI8SKNVmz4sk7buKt/6v9KI65qnm"
        crossorigin="anonymous"></script>


</body>
</html>