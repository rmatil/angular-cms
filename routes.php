<?php

use rmatil\cms\Middleware\BasicAuthMiddleware;
use rmatil\cms\Middleware\SecurityMiddleware;

/**
 * Specifies the routes for the Slim application.
 *
 * For application setup see file setup.php in the root folder of this project.
 */

// See https://github.com/fortrabbit/slimcontroller/issues/23 for overloading methods
$app->addRoutes(array(
    '/'                                 => 'Index:index',
    '/:type/:identifier' => array('get' => 'Index:path'),

    '/install'                          => 'Install:install',
    '/install/do-install'               => 'Install:doInstall',

    // login
    '/login'                            => array('get'     => 'Login:doLogin',
                                                 'post'    => 'Login:doLogin'),
    '/logout'                           => array('get'     => 'Login:doLogout'),

    // articles
    '/api/articles'                     => array('get'     => 'Article:getArticles',
                                                 'post'    => 'Article:insertArticle'),
    '/api/articles/:id'                 => array('get'     => 'Article:getArticleById',
                                                 'put'     => 'Article:updateArticle',
                                                 'delete'  => 'Article:deleteArticleById'),

    // articleCategories
    '/api/article-categories'           => array('get'     => 'ArticleCategory:getArticleCategories',
                                                 'post'    => 'ArticleCategory:insertArticleCategory'),
    '/api/article-categories/:id'       => array('get'     => 'ArticleCategory:getArticleCategoryById',
                                                 'put'     => 'ArticleCategory:updateArticleCategory',
                                                 'delete'  => 'ArticleCategory:deleteArticleCategoryById'),


    // pages
    '/api/pages'                        => array('get'    => 'Page:getPages',
                                                 'post'   => 'Page:insertPage'),
    '/api/pages/:id'                    => array('get'    => 'Page:getPageById',
                                                 'put'    => 'Page:updatePage',
                                                 'delete' => 'Page:deletePageById'),

    // pageCategories
    '/api/page-categories'              => array('get'    => 'PageCategory:getPageCategories',
                                                 'post'   => 'PageCategory:insertPageCategory'),
    '/api/page-categories/:id'          => array('get'    => 'PageCategory:getPageCategoryById',
                                                 'put'    => 'PageCategory:updatePageCategory',
                                                 'delete' => 'PageCategory:deletePageCategoryById'),

    // events
    '/api/events'                       => array('get'     => 'Event:getEvents',
                                                 'post'    => 'Event:insertEvent'),
    '/api/events/:id'                   => array('get'     => 'Event:getEventById',
                                                 'put'     => 'Event:updateEvent',
                                                 'delete'  => 'Event:deleteEventById'),

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
                                                'put'     => 'Location:updateLocation',
                                                'delete'  => 'Location:deleteLocationById'),
    
    // languages
    '/api/languages'                   => array('get'     => 'Language:getLanguages',
                                                'post'    => 'Language:insertLanguage'),
    '/api/languages/:id'               => array('get'     => 'Language:getLanguageById',
                                                'put'     => 'Language:updateLanguage',
                                                'delete'  => 'Language:deleteLanguageById'),

    // users
    '/api/users'                        => array('get'    => 'User:getUsers',
                                                 'post'   => 'User:insertUser'),
    '/api/users/:id'                    => array('get'    => 'User:getUserById',
                                                 'put'    => 'User:updateUser',
                                                 'delete' => 'User:deleteUserById'),

    // usergroup
    '/api/usergroups'                   => array('get'    => 'UserGroup:getUserGroups',
                                                 'post'   => 'UserGroup:insertUserGroup'),
    '/api/usergroups/:id'               => array('get'    => 'UserGroup:getUserGroupById',
                                                 'put'    => 'UserGroup:updateUserGroup',
                                                 'delete' => 'UserGroup:deleteUserGroupById'),

    // registration
    '/api/registration/:token'          => array('post'   => 'Registration:completeRegistration'),

    // settings
    '/api/settings'                     => array('get'    => 'Setting:getSettings'),
    '/api/settings/update/:id'          => array('post'   => 'Setting:updateSettings'),

    // statistics
    '/api/statistics'                   => array('get'    => 'Stat:getStatistics'),


    // empty objects
    '/api/empty/articles'              => array('get'      => 'Article:getEmptyArticle'),
    '/api/empty/pages'                 => array('get'      => 'Page:getEmptyPage'),
    '/api/empty/events'                => array('get'      => 'Event:getEmptyEvent'),
    '/api/empty/locations'             => array('get'      => 'Location:getEmptyLocation'),
    '/api/empty/users'                 => array('get'      => 'User:getEmptyUser'),

));

