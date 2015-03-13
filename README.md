angular-cms
===========

[![Build Status](https://magnum.travis-ci.com/rmatil/angular-cms.svg?token=YH9Jjv7jtWKZMq8uyuEi&branch=master)](https://magnum.travis-ci.com/rmatil/angular-cms)
[![Codacy Badge](https://www.codacy.com/project/badge/a49a99aed4c149f0815fbaf87fe65d74)](https://www.codacy.com)

Angular CMS is a simple Content Management System (CMS) which is based on the structure of a [Slim Application](https://github.com/codeguy/Slim). The content managamenet is implemented as a single page application with AngularJS.

Installation
============
As of now, you have to download the repo via git. Composer will be available in the future.

Then use composer in the root folder to install required dependencies: `composer install`.
In `web/cms/` run `bower install` to download all required frontend packages.

After download, set up the connection to your database in `config/yaml/parameters.yml`. Furthermore, you can setup the credentials for a mailserver which gets used for sending emails for user registration purposes.

Generate the database schema using `vendor/bin/doctrine orm:schema-tool:create` in the root folder of this application.

You also may want to change the locale used for this application, which you can do in this file 
[here](https://github.com/rmatil/angular-cms/tree/v0.1/setup.php#L39). 
Additionally, you can change the path to the media directory for uploaded files [here](https://github.com/rmatil/angular-cms/tree/v0.1/setup.php#L35) and [here](https://github.com/rmatil/angular-cms/tree/v0.1/setup.php#L36). It might be necessary to give your webserver write permissions on this folder.

Login to backend
================
As until now, you have to generate a user entry in the database. Use `sha512` as hash algorithm for field `passwordHash`.
Navigate in your browser to `your-webserver/login` to login with specified username and credentials.

Registration Endpoint
=====================
Registration endpoint is specified at `api/registration/:token`


