angular-cms
===========

[![Build Status](https://travis-ci.org/rmatil/angular-cms.svg?branch=master)](https://travis-ci.org/rmatil/angular-cms)
[![Codacy Badge](https://www.codacy.com/project/badge/29fc1a82158346ddb42cd13cdde3a163)](https://www.codacy.com)


Angular CMS is a Symfony 3.2 application and requires PHP 7.1.

# Installation

## Dependencies

* Clone the repo: `git clone git@github.com:rmatil/angular-cms.git`
* `cd angular-cms`
* run `composer install` to install dependencies

## Setup
As done in many other Symfony projects, you have to setup
your application first, before being able to use a bundle directly.
Luckily, this project contains all the necessary configuration 
for the corresponding bundles, in particular [AngularCmsBundle](https://github.com/rmatil/angular-cms-bundle).

However, you might need to perform the following steps to get 
access to the API allowing to adjust content:

* Configure the database credentials in `app/config/parameters.yml`
  resp. in the corresponding `.dist` file.
* Then, run `php app/console fos:user:create` and follow the steps.
* For accessing the API your user needs admin permmissions: 
  `php app/console fos:user:promote` and use the role `ROLE_SUPER_ADMIN`
  when prompted.   

### File Upload
For uploading files you may need to adjust some settings on 
your php installation:

* Open correct `php.ini`: `sudo vim /etc/php/7.1/fpm/php.ini`
* Check for `file_uploads`. Should be `On`
* Check for `post_max_size`.
* Check for `upload_max_filesize`. Should have the same size as `post_max_size`
* Restart apache
* Make sure, that the upload folder has write permissions: `chmod +x web/uploads`
* Make sure, file uploads work. If not check [http://stackoverflow.com/questions/3586919/why-would-files-be-empty-when-uploading-files-to-php](http://stackoverflow.com/questions/3586919/why-would-files-be-empty-when-uploading-files-to-php)


# API

```
  ----------------------------------- ---------- -------- ------ -----------------------------------
   Name                                 Method     Scheme   Host   Path
  ------------------------------------ ---------- -------- ------ -----------------------------------
  rmatil_cms_get_article_categories    GET        ANY      ANY    /api/v1/article-categories
  rmatil_cms_get_article_category      GET        ANY      ANY    /api/v1/article-categories/{id}
  rmatil_cms_update_article_category   PUT        ANY      ANY    /api/v1/article-categories/{id}
  rmatil_cms_insert_article_category   POST       ANY      ANY    /api/v1/article-categories
  rmatil_cms_delete_article_category   DELETE     ANY      ANY    /api/v1/article-categories/{id}
  rmatil_cms_get_articles              GET        ANY      ANY    /api/v1/articles
  rmatil_cms_get_article               GET        ANY      ANY    /api/v1/articles/{id}
  rmatil_cms_update_article            PUT        ANY      ANY    /api/v1/articles/{id}
  rmatil_cms_insert_article            POST       ANY      ANY    /api/v1/articles
  rmatil_cms_get_events                GET        ANY      ANY    /api/v1/events
  rmatil_cms_get_event                 GET        ANY      ANY    /api/v1/events/{id}
  rmatil_cms_update_event              PUT        ANY      ANY    /api/v1/events/{id}
  rmatil_cms_insert_events             POST       ANY      ANY    /api/v1/events
  rmatil_cms_delete_event              DELETE     ANY      ANY    /api/v1/events/{id}
  rmatil_cms_get_files                 GET        ANY      ANY    /api/v1/files
  rmatil_cms_get_file                  GET        ANY      ANY    /api/v1/files/{id}
  rmatil_cms_insert_file               POST       ANY      ANY    /api/v1/files
  rmatil_cms_delete_file               DELETE     ANY      ANY    /api/v1/files/{id}
  rmatil_cms_get_locations             GET        ANY      ANY    /api/v1/locations
  rmatil_cms_get_location              GET        ANY      ANY    /api/v1/locations/{id}
  rmatil_cms_update_location           PUT        ANY      ANY    /api/v1/locations/{id}
  rmatil_cms_insert_location           POST       ANY      ANY    /api/v1/locations
  rmatil_cms_delete_location           DELETE     ANY      ANY    /api/v1/locations/{id}
  rmatil_cms_get_media_tags            GET        ANY      ANY    /api/v1/media-tags
  rmatil_cms_get_media_tag             GET        ANY      ANY    /api/v1/media-tags/{id}
  rmatil_cms_update_media_tag          PUT        ANY      ANY    /api/v1/media-tags/{id}
  rmatil_cms_insert_media_tag          POST       ANY      ANY    /api/v1/media-tags
  rmatil_cms_delete_article            DELETE     ANY      ANY    /api/v1/articles/{id}
  rmatil_cms_get_pages                 GET        ANY      ANY    /api/v1/pages
  rmatil_cms_get_page                  GET        ANY      ANY    /api/v1/pages/{id}
  rmatil_cms_update_page               PUT        ANY      ANY    /api/v1/pages/{id}
  rmatil_cms_insert_page               POST       ANY      ANY    /api/v1/pages
  rmatil_cms_delete_page               DELETE     ANY      ANY    /api/v1/pages/{id}
```


# Browser Access
If you configure your `/etc/hosts` file to redirect requests
to `dev.cmsv5.rmatil.vagrant` to your webserver, then
the application is automatically ran in `dev` environment.
You can adjust the hostname to an arbitrary value in `web/.htaccess`.


# License

```
MIT License

Copyright (c) 2018 rmatil

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
```
