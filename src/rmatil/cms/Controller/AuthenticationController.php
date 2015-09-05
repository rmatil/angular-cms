<?php


namespace rmatil\cms\Controller;


use rmatil\cms\Constants\HttpStatusCodes;
use rmatil\cms\Exceptions\AccessDeniedException;
use rmatil\cms\Exceptions\UserLockedException;
use rmatil\cms\Exceptions\UserNotFoundException;
use rmatil\cms\Exceptions\WrongCredentialsException;
use rmatil\cms\Response\ResponseFactory;
use RuntimeException;
use SlimController\SlimController;

/**
 * Use the routes specified in this class for authenticating clients
 * through REST like calls, i.e. without displaying a login form at all.
 *
 * @see \rmatil\cms\Controller\LoginController For login through a login form
 *
 * @package rmatil\cms\Controller
 */
class AuthenticationController extends SlimController {

    public function authenticateAction() {
        if (PHP_SESSION_NONE === session_status()) {
            session_start();
        } else {
            $this->app->loginHandler->logout();
        }

        // requires SetEnvIf Authorization "(.*)" HTTP_AUTHORIZATION=$1
        // in htaccess for forwarding Basic-Auth headers
        $auth = $this->app->request->params('username');
        $pw = $this->app->request->params('password');

        if (null === $auth || null === $pw) {
            ResponseFactory::createErrorJsonResponse($this->app, HttpStatusCodes::BAD_REQUEST, 'Username and password must be specified');
        }

        try {
            /** @var \rmatil\cms\Login\LoginHandler $loginHandler */
            $loginHandler = $this->app->loginHandler;
            $loginHandler->login($auth, $pw, $this->app->request->getPath());
        } catch (UserNotFoundException $unfe) {
            ResponseFactory::createErrorJsonResponse($this->app, HttpStatusCodes::NOT_FOUND, $unfe->getMessage());
            return;
        } catch (WrongCredentialsException $wce) {
            ResponseFactory::createErrorJsonResponse($this->app, HttpStatusCodes::FORBIDDEN, $wce->getMessage());
            return;
        } catch (UserLockedException $ule) {
            ResponseFactory::createErrorJsonResponse($this->app, HttpStatusCodes::FORBIDDEN, $ule->getMessage());
            return;
        } catch (AccessDeniedException $ade) {
            ResponseFactory::createErrorJsonResponse($this->app, HttpStatusCodes::FORBIDDEN, $ade->getMessage());
            return;
        }
    }

}