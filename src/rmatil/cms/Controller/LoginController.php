<?php

namespace rmatil\cms\Controller;

use rmatil\cms\Constants\EntityNames;
use rmatil\cms\Constants\HttpStatusCodes;
use rmatil\cms\Utils\PasswordUtils;
use SlimController\SlimController;

class LoginController extends SlimController {

    public function loginViewAction() {
        $this->app->render('login-form.php');
    }

    public function doLoginAction() {
        $entityManager   = $this->app->entityManager;
        $userRepository  = $entityManager->getRepository(EntityNames::USER);

        $username = $this->app->request->params('username');
        $password = $this->app->request->params('password');

        $user = $userRepository->findOneBy(array('userName' => $username));

        if (null === $user) {
            $this->app->render('login-form.php', array('error' => 'password or username is wrong'));
            return;
        }

        if (PasswordUtils::isEqual($password, $user->getPasswordHash())) {
            $_SESSION['user_id'] = $user->getId();
            $_SESSION['user_user_name'] = $user->getUserName();
            $_SESSION['user_first_name'] = $user->getFirstName();
            $_SESSION['user_last_name'] = $user->getLastName();
            $_SESSION['user_last_login_date'] = $user->getLastLoginDate();
            $_SESSION['user_is_logged_in'] = true;
            $this->app->redirect('/');
        }

        $this->app->response->setStatus(HttpStatusCodes::FORBIDDEN);
        $this->app->render('login-form.php', array('error' => 'password or username is wrong'));
    }

    public function doLogoutAction() {
        unset($_SESSION['user']);
        unset($_SESSION['is_logged_in']);
        session_destroy();
        session_write_close();

        $this->app->redirect('/');
    }
}