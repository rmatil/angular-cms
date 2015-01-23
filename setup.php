<?php

use Slim\LogWriter;
use SlimController\Slim;
use JMS\Serializer\SerializerBuilder;
use rmatil\cms\Handler\ThumbnailHandler;
use rmatil\cms\Handler\FileHandler;
use rmatil\cms\Handler\RegistrationHandler;

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
 * Sets local to ch_DE
 *
 * Logs for this application are setup in the folder LOCAL_ROOT/log/cms.log
 *
 * For the specified routes and their corresponding controllers, see file routes.php
 */

// doctrine and password for database and smtp server
require_once('../bootstrap.php');

// protocol of connection (either http or https)
(!isset($_SERVER['HTTPS']) OR $_SERVER['HTTPS']=='off') ? $protocol = 'http://' : $protocol = 'https://';

define('HTTP_ROOT', $protocol.$_SERVER['HTTP_HOST']);
define('LOCAL_ROOT', __DIR__);
define('HTTP_MEDIA_DIR', HTTP_ROOT.'/media');
define('LOCAL_MEDIA_DIR', LOCAL_ROOT.'/web/media');

// set locale to german
$newLocale = setlocale(LC_TIME, 'de_CH.UTF-8', 'de_CH');

session_cache_limiter("public");
session_start();


$thumbnailHandler    = new ThumbnailHandler();
$fileHandler         = new FileHandler(HTTP_MEDIA_DIR, LOCAL_MEDIA_DIR);
$phpMailer           = new PHPMailer();
$registrationHandler = new RegistrationHandler($entityManager, $phpMailer, $mailParams);

// enable this for log writing to file
$logWriter        = new LogWriter(fopen(__DIR__.'/log/cms.log', 'a'));

$app              = new Slim(array(
                                'debug'                      => true, // enable slim exception handler
                                'log.level'                  => \Slim\Log::DEBUG,
                                'log.enabled'                => true, // enable logging
                                'controller.class_prefix'    => 'rmatil\cms\Controller',
                                'controller.class_suffix'    => 'Controller',
                                'controller.method_suffix'   => 'Action',
                                'controller.template_suffix' => 'php',
                                'log.writer'                 => $logWriter, // enable this forl log writing to file
                                'templates.path'             => LOCAL_ROOT.'/web/slim-templates',
                            ));

// Add Doctrine Entity Manager to app
$app->container->singleton('entityManager', function () use ($dbParams, $config, $entityManager) {
    return $entityManager;
});

// Add JMS Serializer to app
$app->container->singleton('serializer', function () {
    return SerializerBuilder::create()->build();
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

include('routes.php');

$app->run();