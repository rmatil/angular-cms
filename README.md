angular-cms
===========

[![Build Status](https://travis-ci.org/rmatil/angular-cms.svg?branch=master)](https://travis-ci.org/rmatil/angular-cms)
[![Codacy Badge](https://www.codacy.com/project/badge/29fc1a82158346ddb42cd13cdde3a163)](https://www.codacy.com)
[[Codeship Badge](https://codeship.com/projects/944c42f0-6465-0133-f5dd-4e069a91af7c/status?branch=master)](https://codeship-com)

Angular CMS is a simple Content Management System (CMS) which is based on the structure of a [Slim Application](https://github.com/codeguy/Slim). The content managamenet itself is implemented as a single page application with AngularJS.

![ScreenShot](/web/media/overview.png)

Installation
============

## Dependencies

* Clone the repo: `git clone git@github.com:rmatil/angular-cms.git`
* `cd angular-cms`
* `git submodule init && git submodule update`
* run `composer install` to install dependencies
* `cd web/cms` and run `bower install`
* `npm install`
* invoke `gulp` in `web/cms`
* Finally, open `web/cms/index.html` and adjust `<base href="/" />` to match the subdirectory to the cms in the webservers document root, i.e. `<base href="/cms/" />` to resolve the links to the assets correctly as well as to allow Angular to use html5 history

## Database

* Navigate to `app/config/parameters.yml` and adjust the parameters in the section `database`. Currently, only `pdo_mysql` is supported as driver.
* Then, invoke the URL `/install` and follow the instructions


