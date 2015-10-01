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

    '/install'                          => 'Install:install',
    '/install/do-install'               => 'Install:doInstall',

    // login
    '/login'                            => array('get'     => 'Login:doLogin',
                                                 'post'    => 'Login:doLogin'),
    '/logout'                           => array('get'     => 'Login:doLogout'),

    '/authenticate'                     => array('post'    => 'Authentication:authenticate'),

    '/registration/:token'              => array('get'     => 'Registration:registerUser',
                                                 'post'    => 'Registration:completeRegistration'),

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
    '/api/repeat-options'               => array('get'     => 'RepeatOption:getRepeatOptions',
                                                 'post'    => 'RepeatOption:insertRepeatOption'),
    '/api/repeat-options/:id'           => array('get'     => 'RepeatOption:getRepeatOptionById',
                                                 'put'     => 'RepeatOption:updateRepeatOption',
                                                 'delete'  => 'RepeatOption:deleteRepeatOptionById'),

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

    // settings
    '/api/settings'                     => array('get'    => 'Setting:getSettings',
                                                 'post'   => 'Setting:postSettings'),
    '/api/settings/:id'                 => array('get'    => 'Setting:getSettingById',
                                                 'put'    => 'Setting:updateSettings',
                                                 'delete' => 'Setting:deleteSetting'),


    // empty objects
    '/api/empty/articles'              => array('get'      => 'Article:getEmptyArticle'),
    '/api/empty/pages'                 => array('get'      => 'Page:getEmptyPage'),
    '/api/empty/events'                => array('get'      => 'Event:getEmptyEvent'),
    '/api/empty/locations'             => array('get'      => 'Location:getEmptyLocation'),
    '/api/empty/users'                 => array('get'      => 'User:getEmptyUser'),

    '/:type/:identifier' => array('get' => 'Index:path'),

));

