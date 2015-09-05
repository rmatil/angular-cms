<?php


namespace rmatil\cms\Middleware;


use rmatil\cms\Constants\HttpStatusCodes;
use rmatil\cms\Exceptions\AccessDeniedException;
use rmatil\cms\Exceptions\UserLockedException;
use rmatil\cms\Exceptions\UserNotFoundException;
use rmatil\cms\Exceptions\WrongCredentialsException;
use rmatil\cms\Response\ResponseFactory;
use Slim\Middleware;

/**
 * Handles Basic Authentication Headers for requests.
 * Displays a login form, if the route is secured and no
 * Basic Authentication Headers are present in the request.
 *
 * @see \rmatil\cms\Login\LoginHandler To specify how routes are secured
 *
 * @package rmatil\cms\Middleware
 */
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
        $this->realm = $realm;
    }

    /**
     * Call
     *
     * Perform actions specific to this middleware and optionally
     * call the next downstream middleware.
     */
    public function call() {
        /** @var \rmatil\cms\Login\LoginHandler $loginHandler */
        $loginHandler = $this->app->loginHandler;

        if ( ! $loginHandler->isRouteProtected($this->app->request->getPath())) {
            // if route is not protected, just forward request to next middleware
            $this->next->call();
            return;
        }

        // requires SetEnvIf Authorization "(.*)" HTTP_AUTHORIZATION=$1
        // in htaccess for forwarding Basic-Auth headers
        $auth = isset($_SERVER['PHP_AUTH_USER']) ? $_SERVER['PHP_AUTH_USER'] : null;
        $pw = isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : null;

        if (null === $auth || null === $pw) {
            // Send 401 along with authenticate field if header is absent and route is protected
            // @link http://tools.ietf.org/html/rfc1945#section-11
            ResponseFactory::createUnauthorizedResponse($this->app, $this->realm);
            return;
        }

        try {
            $user = $loginHandler->authenticateUser($auth, $pw);
            $loginHandler->isGranted($this->app->request->getPath(), $user->getUserGroup()->getRole());
        } catch (UserNotFoundException $unfe) {
            ResponseFactory::createErrorJsonResponse($this->app, HttpStatusCodes::NOT_FOUND, $unfe->getMessage());
            return;
        } catch (WrongCredentialsException $wce) {
            // resend basic auth login form, if credentials are wrong
            ResponseFactory::createUnauthorizedResponse($this->app, $this->realm);
            return;
        } catch (UserLockedException $ule) {
            ResponseFactory::createErrorJsonResponse($this->app, HttpStatusCodes::FORBIDDEN, $ule->getMessage());
            return;
        } catch (AccessDeniedException $ade) {
            ResponseFactory::createErrorJsonResponse($this->app, HttpStatusCodes::FORBIDDEN, $ade->getMessage());
            return;
        }

        $this->next->call();
    }
}