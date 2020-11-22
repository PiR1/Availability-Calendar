<?php
/**
 * Copyright (C) PiR1, Inc - All Rights Reserved
 *    Apache License
 *    Version 2.0, January 2004
 *    http://www.apache.org/licenses/
 *    See Licence file
 *
 * @file      Autoloader.php
 * @author    PiR1
 * @date     25/05/2020 23:25
 */

namespace Calendar;


class Autoloader
{
    static function register()
    {
        spl_autoload_register([__CLASS__, 'autoload']);
    }

    static function autoload($class){
        if (strpos($class, __NAMESPACE__ . '\\') === 0){
            $class = str_replace(__NAMESPACE__ . '\\', '', $class);
            $class = str_replace('\\', '/', $class);
            require '' . $class . '.php';
        }
    }
}