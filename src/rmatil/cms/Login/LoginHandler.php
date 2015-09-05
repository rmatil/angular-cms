<?php


namespace rmatil\cms\Login;


use rmatil\cms\Constants\EntityNames;
use rmatil\cms\Entities\User;
use rmatil\cms\Exceptions\AccessDeniedException;
use rmatil\cms\Exceptions\UserLockedException;
use rmatil\cms\Exceptions\UserNotFoundException;
use rmatil\cms\Exceptions\WrongCredentialsException;

class LoginHandler {

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    protected $securedRoutes;

    public function __construct($em, $securedRoutes) {
        $this->em = $em;
        // set longest element of the same route first
        $this->securedRoutes = $securedRoutes;
        krsort($securedRoutes, SORT_NATURAL | SORT_STRING);
    }

    public function login($userName, $password, $path) {
        if (null === $userName || null === $password) {
            $this->isGranted($path, 'ROLE_ANONYMOUS');
            return;
        }

        $user = $this->authenticateUser($userName, $password);

        if (false === $this->isGranted($path, $user->getUserGroup()->getRole())) {
            throw new AccessDeniedException(sprintf('Access denied for user "%s"', $userName));
        }

        $this->initSessionParams($user);
    }

    public function logout() {
        session_unset();
        session_destroy();


        session_start();
        $_SESSION['user_is_logged_in'] = false;
    }

    public function isGranted($path, $userRole) {
        $routeMatched = false;

        foreach ($this->securedRoutes as $securedRoute => $allowedRoles) {
            // check if current path matches any of the routes
            if (1 === preg_match(sprintf('/%s/', $securedRoute), $path)) {
                $routeMatched = true;
                // we found a matching route, check for role
                if (in_array($userRole, $allowedRoles)) {
                    // user is authorized
                    return;
                }
            }
        }

        if (true === $routeMatched) {
            // only throw if a route exist which should be protected
            throw new AccessDeniedException(sprintf('User does not have the required role'));
        }
    }

    /**
     * Checks if the given route is secured by any access restriction
     *
     * @param $path string The route to check for access restrictions
     * @return bool True, if the route is access restricted, false otherwise
     */
    public function isRouteProtected($path) {
        foreach ($this->securedRoutes as $securedRoute => $allowedRoles) {
            if (1 === preg_match(sprintf('/%s/', $securedRoute), $path) &&
                ! in_array('ROLE_ANONYMOUS', $allowedRoles)) {

                return true;
            }
        }

        return false;
    }

    /**
     * Checks whether a user with the given credentials exist.
     *
     * @param $userName string The username
     * @param $password string The password
     *
     * @return \rmatil\cms\Entities\User The found user, on success
     *
     * @throws \rmatil\cms\Exceptions\UserNotFoundException If the user does not exist
     * @throws \rmatil\cms\Exceptions\WrongCredentialsException If the credentials do not match
     * @throws \rmatil\cms\Exceptions\UserLockedException If the uesr is locked
     */
    public function authenticateUser($userName, $password) {
        $user = $this->em->getRepository(EntityNames::USER)->findOneBy(array('userName' => $userName));

        if ( ! ($user instanceof User)) {
            throw new UserNotFoundException(sprintf('User with username "%s" could not be found', $userName));
        }

        if (false === PasswordHandler::isEqual($password, $user->getPasswordHash())) {
            throw new WrongCredentialsException('Password does not match');
        }

        if (true === $user->getIsLocked()) {
            throw new UserLockedException(sprintf('User with username "%s" is locked', $userName));
        }

        return $user;
    }

    protected function initSessionParams(User $user) {
        $_SESSION['user_id'] = $user->getId();
        $_SESSION['user_user_name'] = $user->getUserName();
        $_SESSION['user_first_name'] = $user->getFirstName();
        $_SESSION['user_last_name'] = $user->getLastName();
        $_SESSION['user_last_login_date'] = $user->getLastLoginDate();
        $_SESSION['user_is_logged_in'] = true;
    }
}