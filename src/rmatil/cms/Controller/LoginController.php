<?php

namespace rmatil\cms\Controller;

use SlimController\SlimController;
use rmatil\cms\Constants\HttpStatusCodes;
use Doctrine\ORM\EntityManager;

class LoginController extends SlimController {

    public function loginViewAction() {
        $this->app->render('login-form.php');
    }

    public function doLoginAction() {
        var_dump("asdf");
        exit();
        $this->app->response->setBody("asdf");
    }

    public function doLogoutAction() {

    }
}