<?php

namespace Library\Controller;

use Library\Mvc\Controller\RestfulController;

class TestingController extends RestfulController {

    protected $_allowedMethod = "get";

    public function indexAction() {
        $driver = new \Behat\Mink\Driver\Selenium2Driver('chrome');
        $session = new \Behat\Mink\Session($driver);
        
        $session->start();
        
        $session->visit('http://library-platform.carlosmadrazo.xyz/');
        
        $page = $session->getPage();
        $emailField = $page->find('named', array('id', 'email'));
        $passField = $page->find('named', array('id', 'password'));
        $loginButton = $page->find('named', array('id', 'normal-login'));

        $emailField->setValue('madrazoreyescarloslazaro@gmail.com');
        $passField->setValue('123456');
        $loginButton->click();

        echo $session->getCurrentUrl();


        if (is_null($loginForm)) {
            die("FAIL");
        } else {
            die("OK");
        }
        
        die;
    }

}
