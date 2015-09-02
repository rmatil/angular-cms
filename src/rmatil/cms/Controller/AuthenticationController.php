<?php


namespace rmatil\cms\Controller;


use rmatil\cms\Constants\HttpStatusCodes;
use RuntimeException;
use SlimController\SlimController;

class AuthenticationController extends SlimController {

    public function authenticateAction() {
        if (PHP_SESSION_NONE === session_status()) {
            session_start();
        }

        if (array_key_exists('user_is_logged_in', $_SESSION) && true === $_SESSION['user_is_logged_in']) {
            // check whether basic auth user matches current session user
            if (isset($_SERVER['PHP_AUTH_USER']) && $_SERVER['PHP_AUTH_USER'] === $_SESSION['user_user_name']) {
                return;
            } else if ( ! isset($_SERVER['PHP_AUTH_USER'])) {
                // no basic auth headers were set
                return;
            }
        }


        // requires SetEnvIf Authorization "(.*)" HTTP_AUTHORIZATION=$1
        // in htaccess for forwarding Basic-Auth headers
        $auth = isset($_SERVER['PHP_AUTH_USER']) ? $_SERVER['PHP_AUTH_USER']: null;
        $pw = isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW']: null;

        try {
            $this->app->loginHandler->login($auth, $pw, $this->app->request->getPath());
        } catch (RuntimeException $ade) {
            $this->app->response->header('Content-Type', 'application/json');
            $this->app->response->status(HttpStatusCodes::FORBIDDEN);
            $this->app->response->body(json_encode(array('error' => HttpStatusCodes::FORBIDDEN, 'message' => $ade->getMessage())));
            return;
        }
    }

}