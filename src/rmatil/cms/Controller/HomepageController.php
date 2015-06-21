<?php

namespace rmatil\cms\Controller;

use SlimController\SlimController;

class HomepageController extends SlimController {

    public function indexAction() {
        $this->app->response->setBody("This is the index page of your Slim application");
    }
}