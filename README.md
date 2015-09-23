angular-cms
===========

[![Build Status](https://travis-ci.org/rmatil/angular-cms.svg?branch=master)](https://travis-ci.org/rmatil/angular-cms)
[![Codacy Badge](https://www.codacy.com/project/badge/29fc1a82158346ddb42cd13cdde3a163)](https://www.codacy.com)

Angular CMS is a simple Content Management System (CMS) which is based on the structure of a [Slim Application](https://github.com/codeguy/Slim). The content managamenet itself is implemented as a single page application with AngularJS.

![ScreenShot](/web/media/overview.png)

Installation
============

## Dependencies

* Clone the repo: `git clone git@github.com:rmatil/angular-cms.git`
* `cd angular-cms`
* run `composer install` to install dependencies
* `cd web/cms` and run `bower install`
* finally invoke `gulp` in `web/cms`

## Database

* Navigate to `app/config/parameters.yml` and adjust the parameters in the section `database`. Currently, only `pdo_mysql` is supported as driver.
* Then, invoke the URL `/install` and follow the instructions


