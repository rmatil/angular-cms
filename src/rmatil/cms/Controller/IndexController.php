<?php

namespace rmatil\cms\Controller;

use SlimController\SlimController;
use rmatil\cms\Constants\HttpStatusCodes;
use rmatil\cms\Entities\Article;
use Doctrine\ORM\EntityManager;

class IndexController extends SlimController {

    public function indexAction() {
        $this->app->render('cms/index.php');
    }
}