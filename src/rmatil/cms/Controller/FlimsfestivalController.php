<?php

namespace rmatil\cms\Controller;

use SlimController\SlimController;
use rmatil\cms\Constants\HttpStatusCodes;
use rmatil\cms\Entities\Article;
use Doctrine\ORM\EntityManager;

class FlimsfestivalController extends SlimController {

    public function indexAction() {
        $this->app->response->setBody("yeee");
    }
}