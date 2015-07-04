<?php

use rmatil\cms\Middleware\SecurityMiddleware;

/**
 * Specifies the routes for the Slim application.
 *
 * For application setup see file setup.php in the root folder of this project.
 */

$app->add(new SecurityMiddleware(array('api')));

// See https://github.com/fortrabbit/slimcontroller/issues/23 for overloading methods
$app->addRoutes(array(
    '/'                                 => 'Homepage:index',
    '/cms'                              => 'Index:index',
    '/install'                          => 'Install:install',
    '/install/do-install'               => 'Install:doInstall',

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

