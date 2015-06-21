<?php

namespace rmatil\cms\Controller;

use SlimController\SlimController;

class IndexController extends SlimController {

    public function indexAction() {
        // $this->app->redirect('cms/');
        $this->app->response->setBody("index action in index controller");
    }
}