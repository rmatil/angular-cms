<?php

namespace rmatil\cms\Controller;

use SlimController\SlimController;
use Symfony\Component\Yaml\Yaml;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\EntityManager;
use rmatil\cms\Constants\EntityNames;
use rmatil\cms\Entities\User;
use rmatil\cms\Entities\UserGroup;
use rmatil\cms\Utils\PasswordUtils;
use DateTime;

class InstallController extends SlimController {

    public function installAction() {
        $this->app->render('install-form.php');
    }
    
    public function doInstallAction() {
        $dbParams = array(
            'driver' => $this->app->request->params('database-type'),
            'user' => $this->app->request->params('db-user'),
            'password' => $this->app->request->params('db-password'),
            'dbname' => $this->app->request->params('db-name'),
            'host' => $this->app->request->params('db-host'),
            // http://php.net/manual/en/ref.pdo-mysql.php#pdo.constants.mysql-attr-init-command
            'driverOptions' => array(1002 => 'SET NAMES utf8')
        );
        
        $mailParams = array(
            'host' => '',
            'smtp_auth' => '',
            'username' => '',
            'password' => '',
            'port' => ''
        );
        
        $userParams = array(
            'username' => $this->app->request->params('admin-user'),
            'password' => $this->app->request->params('admin-password'),
            'email' => $this->app->request->params('admin-email')
        );
        
        $config['database'] = $dbParams;
        $config['mail'] = $mailParams;
        $config['admin'] = $userParams;
        
        $this->rewriteConfig($config);
        $em = EntityManager::create($dbParams, $this->app->entityManager->getConfiguration());
        $this->app->container->singleton('entityManager', function () use ($em) {
            return $em;
        });
        
        $this->initDatabase();
        $this->createAdminUser($config);
    }
    
    protected function rewriteConfig(array $config) {
        $params = Yaml::parse(file_get_contents(CONFIG_FILE));
        
        $params['database']['driver'] = $config['database']['driver'];
        $params['database']['username'] = $config['database']['user'];
        $params['database']['password'] = $config['database']['password'];
        $params['database']['dbname'] = $config['database']['dbname'];
        $params['database']['host'] = $config['database']['host'];
        
        $params['mail']['host'] = $config['mail']['host'];
        $params['mail']['smtp_auth'] = $config['mail']['smtp_auth'];
        $params['mail']['username'] = $config['mail']['username'];
        $params['mail']['password'] = $config['mail']['password'];
        $params['mail']['port'] = $config['mail']['port'];
     
        file_put_contents(CONFIG_FILE, Yaml::dump($params, 2, 4, true));
    }
    
    protected function initDatabase() {
        $entityManager = $this->app->entityManager;
        
        $tool = new SchemaTool($entityManager);

        $classes = array(
            $entityManager->getClassMetadata(EntityNames::ARTICLE),
            $entityManager->getClassMetadata(EntityNames::ARTICLE_CATEGORY),
            $entityManager->getClassMetadata(EntityNames::EVENT),
            $entityManager->getClassMetadata(EntityNames::FILE),
            $entityManager->getClassMetadata(EntityNames::FILE_CATEGORY),
            $entityManager->getClassMetadata(EntityNames::LANGUAGE),
            $entityManager->getClassMetadata(EntityNames::LOCATION),
            $entityManager->getClassMetadata(EntityNames::PAGE),
            $entityManager->getClassMetadata(EntityNames::PAGE_CATEGORY),
            $entityManager->getClassMetadata(EntityNames::REGISTRATION),
            $entityManager->getClassMetadata(EntityNames::REPEAT_OPTION),
            $entityManager->getClassMetadata(EntityNames::SETTING),
            $entityManager->getClassMetadata(EntityNames::USER),
            $entityManager->getClassMetadata(EntityNames::USER_GROUP)
        );


        $tool->createSchema($classes, true);
    }
    
    protected function createAdminUser(array $config) {
        $user = new User;
        $user->setFirstName('');
        $user->setLastName('');
        $user->setUserName($config['admin']['username']);
        $user->setPasswordHash(PasswordUtils::hash($config['admin']['password']));
        $user->setEmail($config['admin']['email']);
        $user->setIsLocked(false);
        
        $userGroup = new UserGroup();
        $userGroup->setName('super_admin');
        $userGroup->setRole('ROLE_SUPER_ADMIN');
        
        $now = new DateTime();
        $user->setRegistrationDate($now);
        $user->setLastLoginDate($now);
        $user->setHasEmailValidated(false);
        $user->setUserGroup($userGroup);
        
        $em = $this->app->entityManager;
        $em->persist($userGroup);
        $em->persist($user);
        $em->flush();
    }
}


