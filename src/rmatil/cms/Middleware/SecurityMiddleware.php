<?php

namespace rmatil\cms\Middleware;

use Slim\Middleware;
use rmatil\cms\Constants\HttpStatusCodes;

class SecurityMiddleware extends Middleware {

    private $securedRoutes;

    /**
     * Constructor
     * @param array $securedRoutes First part of URI after host until first slash
     */
    public function __construct(array $securedRoutes) {
        $this->securedRoutes = $securedRoutes;
    }
    
    public function call() {

        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // something in the form of /api/events/25
        $requestedPath = $this->app->request->getPath();
        $pathParams = explode('/', $requestedPath);

        if ($pathParams !== false && is_array($pathParams) && 
            $pathParams[0] !== $requestedPath &&
            in_array($pathParams[1], $this->securedRoutes)) {

            // check if user is logged in
            if (!isset($_SESSION['user_is_logged_in']) ||
                true !== $_SESSION['user_is_logged_in']) {
                $this->app->response->setStatus(HttpStatusCodes::UNAUTHORIZED);
                return;
            }
        }

        $this->next->call();
    }
}