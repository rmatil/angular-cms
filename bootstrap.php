<?php

require_once "vendor/autoload.php";

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Slim\LogWriter;
use SlimController\Slim;
use JMS\Serializer\SerializerBuilder;
use rmatil\cms\Handler\ThumbnailHandler;
use rmatil\cms\Handler\FileHandler;
use rmatil\cms\Handler\RegistrationHandler;

// If $isDevMode is true caching is done in memory with the ArrayCache. Proxy objects are recreated on every request.
$isDevMode = true;
$config = Setup::createAnnotationMetadataConfiguration(array(__DIR__."/src"), $isDevMode, null, null, false); // false to use @ORM\Annotaion

// register annotations of jms serializer
AnnotationRegistry::registerAutoloadNamespace(
    'JMS\Serializer\Annotation', __DIR__.'/vendor/jms/serializer/src'
);

// the connection configuration
$dbParams = array(
    'driver'   => 'pdo_mysql',
    'user'     => 'root',
    'password' => 'root',
    'dbname'   => 'cms',
);

$mailParams = array(
    'CharSet'  => 'UTF-8',
    'Host'     => '',
    'SMTPAuth' => true,
    'Username' => '',
    'Password' => '',
    'Port'     => 587
);

// protocol of connection (either http or https)
(!isset($_SERVER['HTTPS']) OR $_SERVER['HTTPS']=='off') ? $protocol = 'http://' : $protocol = 'https://';

define('HTTP_ROOT', $protocol.$_SERVER['HTTP_HOST']);
define('LOCAL_ROOT', __DIR__);
define('HTTP_MEDIA_DIR', HTTP_ROOT.'/media');
define('LOCAL_MEDIA_DIR', LOCAL_ROOT.'/web/media');


session_cache_limiter(false);
session_start();

$entityManager       = EntityManager::create($dbParams, $config);
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



// See https://github.com/fortrabbit/slimcontroller/issues/23 for overloading methods
$app->addRoutes(array(
    '/'                                 => 'Flimsfestival:index',
    '/cms'                              => 'Index:index',

    // login
    '/login'                            => array('get'     => 'Login:loginView'),
    '/login/do-login'                   => array('post'    => 'Login:doLogin'),
    '/login/do-logout'                  => array('get'     => 'Login:doLogout'),

    // articles
    '/api/articles'                     => array('get'     => 'Article:getArticles',
                                                 'post'    => 'Article:insertArticle'),
    '/api/articles/:id'                 => array('get'     => 'Article:getArticleById',
                                                 'delete'  => 'Article:deleteArticleById'),
    '/api/articles/update/:id'          => array('post'    => 'Article:updateArticle'),

    // articleCategories
    '/api/articleCategories'            => array('get'     => 'ArticleCategory:getArticleCategories',
                                                 'post'    => 'ArticleCategory:insertArticleCategory'),
    '/api/articleCategories/:id'        => array('get'     => 'ArticleCategory:getArticleCategoryById',
                                                 'delete'  => 'ArticleCategory:deleteArticleCategoryById'),
    '/api/articleCategories/update/:id' => array('post'    => 'ArticleCategory:updateArticleCategory'),


    // pages
    '/api/pages'                        => array('get'    => 'Page:getPages',
                                                 'post'   => 'Page:insertPage'),
    '/api/pages/:id'                    => array('get'    => 'Page:getPageById',
                                                 'delete' => 'Page:deletePageById'),
    '/api/pages/update/:id'             => array('post'   => 'Page:updatePage'),

    // pageCategories
    '/api/pageCategories'               => array('get'    => 'PageCategory:getPageCategories',
                                                 'post'   => 'PageCategory:insertPageCategory'),
    '/api/pageCategories/:id'           => array('get'    => 'PageCategory:getPageCategoryById',
                                                 'delete' => 'PageCategory:deletePageCategoryById'),
    '/api/pageCategories/update/:id'    => array('post'   => 'PageCategory:updatePageCategory'),

    // events
    '/api/events'                      => array('get'     => 'Event:getEvents',
                                                'post'    => 'Event:insertEvent'),
    '/api/events/:id'                  => array('get'     => 'Event:getEventById',
                                                'delete'  => 'Event:deleteEventById'),
    '/api/events/update/:id'           => array('post'    => 'Event:updateEvent'),

    // events
    '/api/repeatOptions'               => array('get'     => 'RepeatOption:getRepeatOptions',
                                                'post'    => 'RepeatOption:insertRepeatOption'),
    '/api/repeatOptions/:id'           => array('get'     => 'RepeatOption:getRepeatOptionById',
                                                'delete'  => 'RepeatOption:deleteRepeatOptionById'),
    '/api/repeatOptions/update/:id'    => array('post'    => 'RepeatOption:updateRepeatOption'),

    // files
    '/api/files'                       => array('get'     => 'File:getFiles',
                                                'post'    => 'File:insertFile'),
    '/api/files/:id'                   => array('get'     => 'File:getFileById',
                                                'delete'  => 'File:deleteFileById'),
    // locations
    '/api/locations'                   => array('get'     => 'Location:getLocations',
                                                'post'    => 'Location:insertLocation'),
    '/api/locations/:id'               => array('get'     => 'Location:getLocationById',
                                                'delete'  => 'Location:deleteLocationById'),
    '/api/locations/update/:id'        => array('post'    => 'Location:updateLocation'),
    
    // languages
    '/api/languages'                   => array('get'     => 'Language:getLanguages',
                                                'post'    => 'Language:insertLanguage'),
    '/api/languages/:id'               => array('get'     => 'Language:getLanguageById',
                                                'delete'  => 'Language:deleteLanguageById'),
    '/api/languages/update/:id'        => array('post'    => 'Language:updateLanguages'),

    // users
    '/api/users'                        => array('get'    => 'User:getUsers',
                                                 'post'   => 'User:insertUser'),
    '/api/users/:id'                    => array('get'    => 'User:getUserById',
                                                 'delete' => 'User:deleteUserById'),
    '/api/users/update/:id'             => array('post'   => 'User:updateUser'),

    // usergroup
    '/api/usergroups'                   => array('get'    => 'UserGroup:getUserGroups',
                                                 'post'   => 'UserGroup:insertUserGroup'),
    '/api/usergroups/:id'               => array('get'    => 'UserGroup:getUserGroupById',
                                                 'delete' => 'UserGroup:deleteUserGroupById'),
    '/api/usergroups/update/:id'        => array('post'   => 'UserGroup:updateUserGroup'),

    // registration
    '/api/registration/:token'          => array('post'   => 'Registration:completeRegistration'),

    // settings
    '/api/settings'                     => array('get'    => 'Setting:getSettings'),
    '/api/settings/update/:id'          => array('post'   => 'Setting:updateSettings'),

    // statistics
    '/api/statistics'                   => array('get'    => 'Stat:getStatistics'),


    // empty objects
    '/api/empty/article'              => array('get'      => 'Article:getEmptyArticle'),
    '/api/empty/page'                 => array('get'      => 'Page:getEmptyPage'),
    '/api/empty/event'                => array('get'      => 'Event:getEmptyEvent'),
    '/api/empty/location'             => array('get'      => 'Location:getEmptyLocation'),
    '/api/empty/user'                 => array('get'      => 'User:getEmptyUser'),

));


// comment this out when updating database with doctrine cli
$app->run();






