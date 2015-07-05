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
<html lang="de" ng-app="cms">
<head>
    <!-- Angular will resolve all rel. links to this base -->
    <base href="/cms/" />
    <meta charset="utf-8">
    <title>Backend</title>
    <!-- stylesheets -->
    <link rel="stylesheet" href="css/style.css" />

    <link rel="stylesheet" href="bower_components/AngularJS-Toaster/toaster.css" />
    <link rel="stylesheet" href="css/toaster.css" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="bower_components/angular-pickadate/src/angular-pickadate.css" />
    <link rel="stylesheet" href="bower_components/ng-ckeditor/ng-ckeditor.css" />

    <!-- libraries -->
    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>

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
    
    <!-- app -->
    <script src="js/build/app.min.js"></script>
    <script src="js/build/services.min.js"></script>
    <script src="js/build/genServices.min.js"></script>
    <script src="js/build/filters.min.js"></script>
    <script src="js/build/directives.min.js"></script>

    <script src="js/build/controllers.min.js"></script>
    <script src="js/build/overviewCtrl.min.js"></script>
    <script src="js/build/articleCtrl.min.js"></script>
    <script src="js/build/pageCtrl.min.js"></script>
    <script src="js/build/eventCtrl.min.js"></script>
    <script src="js/build/locationCtrl.min.js"></script>
    <script src="js/build/mediaCtrl.min.js"></script>
    <script src="js/build/userCtrl.min.js"></script>
    <script src="js/build/settingCtrl.min.js"></script>
    <script src="js/build/errorCtrl.min.js"></script>
    <!--
    <script src="js/app.js"></script>
    <script src="js/services.js"></script>
    <script src="js/genServices.js"></script>
    <script src="js/filters.js"></script>
    <script src="js/directives.js"></script>

    <script src="js/controller/controllers.js"></script>
    <script src="js/controller/overviewCtrl.js"></script>
    <script src="js/controller/articleCtrl.js"></script>
    <script src="js/controller/pageCtrl.js"></script>
    <script src="js/controller/eventCtrl.js"></script>
    <script src="js/controller/locationCtrl.js"></script>
    <script src="js/controller/mediaCtrl.js"></script>
    <script src="js/controller/userCtrl.js"></script>
    <script src="js/controller/settingCtrl.js"></script>
    <script src="js/controller/errorCtrl.js"></script>
    -->
</head>
<body>
    <div class="overlay" ng-class="{'hidden': isLocked != true}">
        <div class="overlay-message">
            {{ isLockedMessage }} <br />
            <button ng-click="isLocked = !isLocked">OK</button> <button ng-click="" go-back>Zurück</button>
        </div>
    </div>
    <div class="wrapper">
        <div class="header">
            <div class="header-content"></div>
        </div>
        <div class="left-sidebar">
            <div class="left-sidebar-bar"></div>
            <div ng-cloak class="left-sidebar-current-active">
                <ul>
                    <li class="{{backgroundColorClass}} text-center">{{ activeMenuPoint }}</li>
                </ul>
            </div>
            <div class="left-sidebar-nav">
                <ul class="navigation">
                    <li class="lightyellow-border">
                        <a href="#/overview"><i class="icon-box fa fa-tachometer"></i> Übersicht</a>
                    </li>
                    <li class="darkred-border">
                        <a href="#/articles"><i class="icon-box fa fa-file-text-o"></i> Artikel</a>
                    </li>
                    <li class="darkpink-border">
                        <a href="#/events"><i class="icon-box fa fa-calendar"></i> Veranstaltungen</a>
                    </li>
                    <li class="darkpurple-border">
                        <a href="#/files"><i class="icon-box fa fa-picture-o"></i> Media</a>
                    </li>
                    <li class="darkblue-border">
                        <a href="#/users"><i class="icon-box fa fa-users"></i> Benutzer</a>
                    </li>
                    <li class="darkgreen-border">
                        <a href="#/settings"><i class="icon-box fa fa-cog"></i> Einstellungen</a>
                    </li>
                </ul>
            </div>
            <toaster-container toaster-options="{'position-class': 'toast-top-full-width'}"></toaster-container>
        </div>
        <div class="content-bar-wrapper">
            <div class="content-bar">
                <ul class="icon-bar">
                    <li >
                        <i class="fa fa-user"></i> 
                        <?php
                            echo sprintf("<a class='no-underline' href='/cms/users/%s'>%s</a>", $_SESSION['user_id'], $_SESSION['user_first_name']);
                        ?>
                    </li>
                    <li>
                        <i class="fa fa-calendar"></i> 
                        <?php
                            $now = new \DateTime();
                            echo $now->format('d.m.Y');
                        ?>
                    </li>
                    <li>
                        <i class="fa fa-sign-out"></i> 
                        <a class="no-underline" href="/login/do-logout" target="_self">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
        <div ng-cloak class="content-nav">
            <ul>
                <li ng-repeat="menuEntry in menuArray" class="{{ topBorderClass }}">
                    <a href="{{ menuEntry.link }}">{{ menuEntry.name }}</a>
                </li>
            </ul>
        </div>
        <div ng-cloak class="content">
            <div ng-view class="content-text"></div>
        </div>
    </div>
</body>
</html>
