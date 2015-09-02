<?php


namespace rmatil\cms\Middleware;


use rmatil\cms\Constants\HttpStatusCodes;
use rmatil\cms\Exceptions\AccessDeniedException;
use rmatil\cms\Exceptions\UserNotFoundException;
use RuntimeException;
use Slim\Middleware;

class BasicAuthMiddleware extends Middleware {

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var string
     */
    protected $realm = '';

    public function __construct($em, $realm) {
        $this->em = $em;
    }

    /**
     * Call
     *
     * Perform actions specific to this middleware and optionally
     * call the next downstream middleware.
     */
    public function call() {
        if (PHP_SESSION_NONE === session_status()) {
            session_start();
        }

        if (array_key_exists('user_is_logged_in', $_SESSION) && true === $_SESSION['user_is_logged_in']) {
            // check whether basic auth user matches current session user
            if (isset($_SERVER['PHP_AUTH_USER']) && $_SERVER['PHP_AUTH_USER'] === $_SESSION['user_user_name']) {
                $this->next->call();
                return;
            } else if ( ! isset($_SERVER['PHP_AUTH_USER'])) {
                // no basic auth headers were set
                $this->next->call();
                return;
            }
        }


        // requires SetEnvIf Authorization "(.*)" HTTP_AUTHORIZATION=$1
        // in htaccess for forwarding Basic-Auth headers
        $auth = isset($_SERVER['PHP_AUTH_USER']) ? $_SERVER['PHP_AUTH_USER']: null;
        $pw = isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW']: null;

        try {
            $this->app->loginHandler->login($auth, $pw, $this->app->request->getPath());
        } catch (AccessDeniedException $ade) {
            $this->app->response->header('Content-Type', 'application/json');
            $this->app->response->status(HttpStatusCodes::FORBIDDEN);
            $this->app->response->body(json_encode(array('error' => HttpStatusCodes::FORBIDDEN, 'message' => $ade->getMessage())));
            return;
        } catch (RuntimeException $re) {
            // password is wrong or user does not exist
            $this->requestLogin();
            return;
        }

        $this->next->call();
    }

    protected function requestLogin() {
        $this->app->response->status(HttpStatusCodes::UNAUTHORIZED);
        $this->app->response->header('WWW-Authenticate', sprintf('Basic realm="%s"', $this->realm));
    }
}