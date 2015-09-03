<?php

namespace rmatil\cms\Controller;

use DateTime;
use Doctrine\ORM\EntityManager;
use JMS\Serializer\SerializerBuilder;
use rmatil\cms\Constants\ConfigurationNames;
use rmatil\cms\Entities\User;
use rmatil\cms\Entities\UserGroup;
use rmatil\cms\Handler\ConfigurationHandler;
use rmatil\cms\Handler\HandlerSingleton;
use rmatil\cms\Utils\EntityManagerFactory;
use rmatil\cms\Utils\PasswordUtils;
use SlimController\SlimController;

class InstallController extends SlimController {

    public function installAction() {
        if ($this->app->databaseHandler->schemaExists()) {
            // installation is already done
            return $this->app->redirect('/login');
        }
        $this->app->render('install-form.html.twig');
    }

    public function doInstallAction() {
        if ($this->app->databaseHandler->schemaExists()) {
            // installation is already done
            return $this->app->redirect('/login');
        }

        $dbParams = array(
            ConfigurationNames::DB_DRIVER => $this->app->request->params('database-type'),
            ConfigurationNames::DB_USER => $this->app->request->params('db-user'),
            ConfigurationNames::DB_PASSWORD => $this->app->request->params('db-password'),
            ConfigurationNames::DB_DBNAME => $this->app->request->params('db-name'),
            ConfigurationNames::DB_HOST => $this->app->request->params('db-host'),
            // http://php.net/manual/en/ref.pdo-mysql.php#pdo.constants.mysql-attr-init-command
            ConfigurationNames::DB_DRIVER_OPTIONS => array(1002 => 'SET NAMES utf8')
        );
        
        $mailParams = array(
            ConfigurationNames::MAIL_HOST => $this->app->request->params('mail-host'),
            ConfigurationNames::MAIL_SMTP_AUTH => boolval($this->app->request->params('mail-smtp-auth')),
            ConfigurationNames::MAIL_USERNAME => $this->app->request->params('mail-username'),
            ConfigurationNames::MAIL_PASSWORD => $this->app->request->params('mail-password'),
            ConfigurationNames::MAIL_PORT => intval($this->app->request->params('mail-port'))
        );
        
        $userParams = array(
            ConfigurationNames::ADMIN_USERNAME => $this->app->request->params('admin-user'),
            ConfigurationNames::ADMIN_PASSWORD => $this->app->request->params('admin-password'),
            ConfigurationNames::ADMIN_EMAIL => $this->app->request->params('admin-email')
        );
        
        $config[ConfigurationNames::DATABASE_PREFIX] = $dbParams;
        $config[ConfigurationNames::MAIL_PREFIX] = $mailParams;
        $config[ConfigurationNames::ADMIN_PREFIX] = $userParams;
        
        try {            
            ConfigurationHandler::writeConfiguration($config, CONFIG_FILE);
            // uses the freshly written config file params
            $em = EntityManagerFactory::createEntityManager(\CONFIG_FILE, \SRC_FOLDER, true);
            
            // inits singletons, like all handlers
            $this->reinitAppSingletons($em);
            $this->app->databaseHandler->setupDatabase();
            $this->app->databaseHandler->initDatabaseSettings(
                $this->app->request->params('website-name'),
                $this->app->request->params('website-email'),
                $this->app->request->params('website-reply-to-email'),
                $this->app->request->params('website-url')
            );
            $this->createAdminUser($config);
        } catch (Exception $e) {
            $this->app->databaseHandler->deleteDatabase();
            return $this->app->render('install-form.html.twig', array('errors' => array($e->getMessage(), $e->getTraceAsString())));
        }
        
        $this->app->redirect('/login');
    }

    /**
     * Creates the admin user based on the configuration data.<br>
     * Required keys in $config are
     * <ul>
     *   <li>[admin][username]</li>
     *   <li>[admin][password]</li>
     *   <li>[admin][email]</li>
     * </ul>
     *
     * @param array $config The array holding the above keys
     */
    protected function createAdminUser(array $config) {
        $user = new User;
        $user->setFirstName('');
        $user->setLastName('');
        $user->setUserName($config['admin']['username']);
        $user->setPasswordHash(PasswordUtils::hash($config['admin']['password']));
        $user->setEmail($config['admin']['email']);
        $user->setIsLocked(false);
        
        $userGroup = new UserGroup();
        $userGroup->setName('Super Admin');
        $userGroup->setRole('ROLE_SUPER_ADMIN');


        $now = new DateTime();
        $user->setRegistrationDate($now);
        $user->setLastLoginDate($now);
        $user->setHasEmailValidated(false);
        $user->setUserGroup($userGroup);

        $em = $this->app->entityManager;
        $em->persist($userGroup);
        $em->flush();

        $this->app->registrationHandler->registerUser($user);
    }

    /**
     * Reinitialises the singletons used by this app to make
     * use of the newly configured parameters.
     *
     * @param EntityManager $entityManager The entity manager created with the new parameters
     */
    protected function reinitAppSingletons(EntityManager $entityManager) {
        $this->app->container->singleton('entityManager', function () use ($entityManager) {
            return $entityManager;
        });

        // Add JMS Serializer to app
        $this->app->container->singleton('serializer', function () {
            return SerializerBuilder::create()->build();
        });

        HandlerSingleton::setEntityManager($this->app->container->entityManager);
        $thumbnailHandler = HandlerSingleton::getThumbnailHandler();
        $fileHandler = HandlerSingleton::getFileHandler(\HTTP_MEDIA_DIR, \LOCAL_MEDIA_DIR);
        $registrationHandler = HandlerSingleton::getRegistrationHandler();
        $databaseHandler = HandlerSingleton::getDatabaseHandler();

        // Add Doctrine Entity Manager to app
        $this->app->container->remove('entityManager');
        $this->app->container->singleton('entityManager', function () use ($entityManager) {
            return $entityManager;
        });

        $this->app->container->remove('databaseHandler');
        $this->app->container->singleton('databaseHandler', function () use ($databaseHandler) {
            return $databaseHandler;
        });

        // Add thumbnail handler to app
        $this->app->container->remove('thumbnailHandler');
        $this->app->container->singleton('thumbnailHandler', function () use ($thumbnailHandler) {
            return $thumbnailHandler;
        });

        // add file handler to app
        $this->app->container->remove('fileHandler');
        $this->app->container->singleton('fileHandler', function () use ($fileHandler) {
            return $fileHandler;
        });
        
        $this->app->container->remove('registrationHandler');
        $this->app->container->singleton('registrationHandler', function() use ($registrationHandler) {
            return $registrationHandler;
        });
    }
}


