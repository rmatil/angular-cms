<?php

use rmatil\cms\Middleware\BasicAuthMiddleware;
use Slim\LogWriter;
use SlimController\Slim;
use JMS\Serializer\SerializerBuilder;
use rmatil\cms\Handler\HandlerSingleton;
use rmatil\cms\Utils\EntityManagerFactory;

/**
 * Setup of Slim application. 
 * See http://docs.slimframework.com/ for docuemntation
 *
 * Defines constants for:
 *     - HTTP_ROOT: http root of this homepage
 *     - LOCAL_ROOT: local root of this homepage. Equals the current directory
 *     - HTTP_MEDIA_DIR: url to media directory
 *     - HTTP_LOCAL_DIR: path to local media directory
 *
 * Sets locale to ch_DE
 *
 * Logs for this application are setup in the folder LOCAL_ROOT/log/cms.log
 *
 * For the specified routes and their corresponding controllers, see file routes.php
 */

// doctrine and password for database and smtp server
require_once('bootstrap.php');

// protocol of connection (either http or https)
(!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === 'off') ? $protocol = 'http://' : $protocol = 'https://';

define('PROTOCOL', $protocol);
define('HTTP_ROOT', $protocol.$_SERVER['HTTP_HOST']);
define('LOCAL_ROOT', __DIR__);
define('HTTP_MEDIA_DIR', HTTP_ROOT.'/media');
define('LOCAL_MEDIA_DIR', LOCAL_ROOT.'/web/media');
define('CONFIG_FILE', LOCAL_ROOT.'/app/config/parameters.yml');
define('SRC_FOLDER', LOCAL_ROOT.'/src');

// set locale to german
$newLocale = setlocale(LC_TIME, 'de_CH.UTF-8', 'de_CH');

// prevent PHP from sending conflicting cache expiration headers with the HTTP response
session_cache_limiter(false);
session_start();

// enable this for log writing to file
$logWriter        = new LogWriter(fopen(__DIR__ . '/app/log/cms.log', 'a'));

$config = \rmatil\cms\Handler\ConfigurationHandler::readConfiguration(CONFIG_FILE);

$app = new Slim(array(
    'debug'                      => true, // enable slim exception handler
    'log.level'                  => \Slim\Log::DEBUG,
    'log.enabled'                => true, // enable logging
    'controller.class_prefix'    => 'rmatil\cms\Controller',
    'controller.class_suffix'    => 'Controller',
    'controller.method_suffix'   => 'Action',
    'controller.template_suffix' => 'php',
    'log.writer'                 => $logWriter, // enable this for log writing to file
    'templates.path'             => LOCAL_ROOT . '/web/templates/' . $config[\rmatil\cms\Constants\ConfigurationNames::TEMPLATE][\rmatil\cms\Constants\ConfigurationNames::TEMPLATE_PATH],
    'view'                       => new \Slim\Views\Twig()
));

$view = $app->view();
$view->parserOptions = array(
    'debug' => true,
    'cache' => __DIR__ . '/app/cache'
);
$view->parserExtensions = array(
    new \Slim\Views\TwigExtension(),
    new \Twig_Extension_Debug(),
);

// Add JMS Serializer to app
$app->container->singleton('serializer', function () {
    return SerializerBuilder::create()->build();
});

HandlerSingleton::setEntityManager($entityManager);
$thumbnailHandler = HandlerSingleton::getThumbnailHandler();
$fileHandler = HandlerSingleton::getFileHandler(HTTP_MEDIA_DIR, LOCAL_MEDIA_DIR);
$registrationHandler = HandlerSingleton::getRegistrationHandler();
$databaseHandler = HandlerSingleton::getDatabaseHandler();
$loginHandler = HandlerSingleton::getLoginHandler(array(
    '^\/api\/.*' => array('ROLE_SUPER_ADMIN')
));

// Add Doctrine Entity Manager to app
$app->container->singleton('entityManager', function () use ($entityManager) {
    return $entityManager;
});

$app->container->singleton('databaseHandler', function () use ($databaseHandler) {
    return $databaseHandler;
});

// Add thumbnail handler to app
$app->container->singleton('thumbnailHandler', function () use ($thumbnailHandler) {
    return $thumbnailHandler;
});

// add file handler to app
$app->container->singleton('fileHandler', function () use ($fileHandler) {
    return $fileHandler;
});

$app->container->singleton('registrationHandler', function() use ($registrationHandler) {
    return $registrationHandler;
});

$app->container->singleton('loginHandler', function () use ($loginHandler) {
    return $loginHandler;
});

// Add Basic Auth Security
$app->add(new BasicAuthMiddleware($entityManager, 'Secured Area'));

$corsOptions = array(
    "origin" => "http://cms-frontend.dev.local",
    "maxAge" => 1728000,
    "allowCredentials" => true,
    "allowHeaders" => array("X-PINGOTHER", "Authorization", "Content-Type"),
    "allowMethods" => array("POST", "GET", "DELETE", "PUT", "OPTIONS", "HEAD")
);
$cors = new \CorsSlim\CorsSlim($corsOptions);
$app->add($cors);

$twig = $app->view()->getEnvironment();
$twig->addExtension(new \rmatil\cms\Twig\MetadataFunction($entityManager));

include('routes.php');

$app->run();