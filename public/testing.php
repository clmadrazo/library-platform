<?php

use Behat\Mink\Driver\Selenium2Driver;
use Behat\Mink\Selector;

error_reporting(E_ALL);
ini_set("display_errors", 1);
ini_set("display_startup_errors", 1);



$classesDir = array (
    '../vendor/behat/mink-selenium2-driver/src/',
    '../vendor/behat/mink/src/Driver/',
    '../vendor/behat/mink/src/Selector',
);

function __autoload($class_name) {
    global $classesDir;
    foreach ($classesDir as $directory) {
        if (file_exists($directory . $class_name . '.php')) {
            require_once ($directory . $class_name . '.php');
            return;
        }
    }
}

$driver = new \Behat\Mink\Driver\Selenium2Driver();
$session = new \Behat\Mink\Session($driver);
