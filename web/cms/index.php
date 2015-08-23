<?php
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['user_is_logged_in']) ||
        true !== $_SESSION['user_is_logged_in']) {
        header('location: ../login');
    }
?>
<!doctype html>
<html class="no-js" lang="en" ng-app="cms">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Backend</title>
    <meta name="description" content="An AngularJS Content Management System">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- required for html5 history api usage -->
    <base href="/cms/" />

    <!-- Place favicon.ico in the root directory -->
    <link rel="apple-touch-icon" href="apple-touch-icon.png">
    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" href="css/main.css">
    <script src="bower_components/modernizr/modernizr.js"></script>



    <link rel="stylesheet" href="bower_components/AngularJS-Toaster/toaster.css" />
    <link rel="stylesheet" href="css/toaster.css" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="bower_components/angular-pickadate/src/angular-pickadate.css" />
    <link rel="stylesheet" href="bower_components/ng-ckeditor/ng-ckeditor.css" />
</head>
<body>
    <!--[if lt IE 8]>
    <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
    <![endif]-->

    <div class="wrapper">
        <div class="header">
            <div class="header-content"></div>
        </div>

        <!-- navigation -->
        <cms-nav></cms-nav>

        <main ng-cloak ng-view></main>
    </div>

    <!-- assets -->
<!--    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>-->
    <script>window.jQuery || document.write('<script src="bower_components/jquery/dist/jquery.min.js"><\/script>')</script>

    <!-- libraries -->
<!--    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>-->

    <script src="bower_components/angular/angular.js"></script>
    <script src="bower_components/angular-animate/angular-animate.min.js" ></script>
    <script src="bower_components/angular-route/angular-route.min.js"></script>
    <script src="bower_components/angular-cookies/angular-cookies.min.js"></script>

    <script src="bower_components/ng-file-upload/angular-file-upload-shim.min.js"></script>
    <script src="bower_components/ng-file-upload/angular-file-upload.min.js"></script>
    <script src="bower_components/angular-pickadate/src/angular-pickadate.js"></script>
    <script src="bower_components/ckeditor/ckeditor.js"></script>
    <script src="bower_components/ng-ckeditor/ng-ckeditor.min.js"></script>
    <script src="bower_components/AngularJS-Toaster/toaster.js"></script>
    <script src="bower_components/angular-local-storage/dist/angular-local-storage.min.js"></script>
    <script src="bower_components/moment/min/moment.min.js"></script>
    <script src="bower_components/angular-momentjs/angular-momentjs.min.js"></script>
    <script src="bower_components/raphael/raphael-min.js"></script>

    <!-- app -->

    <script src="components/app.modules.js"></script>

    <!-- services -->
    <script src="components/navigation/navigationService.js"></script>
    <script src="components/api/genericApiService.js"></script>
    <script src="components/article/articleService.js"></script>
    <script src="components/page/pageService.js"></script>
    <script src="components/language/languageService.js"></script>
    <script src="components/article-category/articleCategoryService.js"></script>
    <script src="components/page-category/pageCategoryService.js"></script>
    <script src="components/location/locationService.js"></script>
    <script src="components/event/eventService.js"></script>
    <script src="components/util/arrayService.js"></script>
    <script src="components/util/stringService.js"></script>
    <script src="components/logger/loggerService.js"></script>

    <!-- directives -->
    <script src="components/navigation/navigation.js"></script>
    <script src="components/status-bar/statusBar.js"></script>
    <script src="components/event-dashboard/eventGraphDirective.js"></script>

    <!-- controllers -->
    <script src="components/article/articleController.js"></script>
    <script src="components/page/pageController.js"></script>
    <script src="components/dashboard/dashboardController.js"></script>
    <script src="components/event-dashboard/eventDashboardController.js"></script>

    <!-- run app -->
    <script src="components/app.js"></script>
</body>
</html>
