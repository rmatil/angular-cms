<?php

namespace rmatil\cms\Controller;

use DateTime;
use Exception;
use rmatil\cms\Constants\ConfigurationNames;
use rmatil\cms\Constants\EntityNames;
use rmatil\cms\Entities\User;
use rmatil\cms\Login\PasswordHandler;
use rmatil\cms\Utils\PasswordUtils;
use SlimController\SlimController;

/**
 * @package rmatil\cms\Controller
 */
class InstallController extends SlimController {

    public function installAction() {
        if ($this->app->databaseHandler->schemaExists()) {
            // installation is already done
            $this->app->redirect('/login');
            return;
        }

        $this->app->render('install-form.html.twig');
    }

    public function doInstallAction() {
        if ($this->app->databaseHandler->schemaExists()) {
            // installation is already done
            $this->app->redirect('/login');
            return;
        }

        $userParams = array(
            ConfigurationNames::ADMIN_USERNAME => $this->app->request->params('admin-user'),
            ConfigurationNames::ADMIN_PASSWORD => $this->app->request->params('admin-password'),
            ConfigurationNames::ADMIN_EMAIL => $this->app->request->params('admin-email')
        );

        try {
            $this->app->databaseHandler->setupDatabase();
            $this->app->databaseHandler->initDatabaseSettings(
                $this->app->request->params('website-name'),
                $this->app->request->params('website-email'),
                $this->app->request->params('website-reply-to-email'),
                $this->app->request->params('website-url')
            );
            $this->createAdminUser($userParams);
        } catch (Exception $e) {
            $this->app->databaseHandler->deleteDatabase();
            $this->app->flashNow('error', $e->getMessage());
            $this->app->render('install-form.html.twig');
            return;
        }

        $this->app->redirect('/login');
    }

    /**
     * Creates the admin user based on the configuration data.<br>
     * Required keys in $config are
     * <ul>
     *   <li>[username]</li>
     *   <li>[password]</li>
     *   <li>[email]</li>
     * </ul>
     *
     * @param array $config The array holding the above keys
     */
    protected function createAdminUser(array $config) {
        $userGroup = $this->app->entityManager->getRepository(EntityNames::USER_GROUP)->findOneBy(array('name' => 'Admin'));
        $now = new DateTime();

        $user = new User;
        $user->setFirstName('');
        $user->setLastName('');
        $user->setUserName($config['username']);
        $user->setPasswordHash(PasswordHandler::hash($config['password']));
        $user->setEmail($config['email']);
        $user->setIsLocked(false);
        $user->setUserGroup($userGroup);
        $user->setRegistrationDate($now);
        $user->setLastLoginDate($now);
        $user->setHasEmailValidated(false);

        $this->app->registrationHandler->registerUser($user);
    }
}


