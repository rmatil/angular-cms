<?php

namespace rmatil\cms\Controller;

use rmatil\cms\Utils\PasswordUtils;
use RuntimeException;
use SlimController\SlimController;

/**
 * @package rmatil\cms\Controller
 */
class LoginController extends SlimController {

    const LOGIN_ERROR = 'login.error';
    const LOGIN_FORWARD = 'login.forward';

    public function doLoginAction() {
        $username = $this->app->request->params('username');
        $password = $this->app->request->params('password');
        $forwardUrl = $this->app->request->params('forward');

        if (null === $username && null === $password) {
            if (null !== $forwardUrl) {
                $this->app->flashNow(self::LOGIN_FORWARD, $forwardUrl);
            }
            $this->app->render('login-form.html.twig');
            return;
        }

        /** @var \rmatil\cms\Login\LoginHandler $loginHandler */
        $loginHandler = $this->app->loginHandler;
        try {
            $loginHandler->login($username, $password, $this->app->request->getPath());
        } catch (RuntimeException $re) {
            $this->app->flashNow(self::LOGIN_ERROR, $re->getMessage());
            $this->app->render('login-form.html.twig');
            return;
        }

        if (null !== $forwardUrl) {
            $this->app->redirect(urldecode($forwardUrl));
        }

        $this->app->redirect('/');
    }

    public function doLogoutAction() {
        $this->app->loginHandler->logout();

        $this->redirect('/');
    }
}