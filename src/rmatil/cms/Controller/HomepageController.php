<?php

namespace rmatil\cms\Controller;

use SlimController\SlimController;
use rmatil\cms\Constants\HttpStatusCodes;
use rmatil\cms\Entities\Article;
use Doctrine\ORM\EntityManager;

class HomepageController extends SlimController {

    public function indexAction() {
        $this->app->response->setBody("This is the index page of your Slim application");
    }
}