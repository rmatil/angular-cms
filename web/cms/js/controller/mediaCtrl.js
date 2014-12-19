'use strict';

angular.module('cms.controllers')
    .controller('mediaCtrl', ['genService', 'MenuService', '$scope', '$timeout', function (genService, MenuService, $scope, $timeout) {
        // set menu according to its Name
        MenuService.update("Media");

        $scope.apiPath   = 'files';
        $scope.menuName  = 'Veranstaltung bearbeiten';
        $scope.deleteMsg = 'Löschen';

        // get all uploaded files
        $scope.loadingFiles = true;
        genService.getAllObjects('files').then(function (response) {
            $scope.files = response;

            $timeout(function () {
                $scope.loadingFiles = false;
            }, 300);
        });
    }])
    .controller('mediaAddCtrl', ['MenuService', '$scope', '$upload', '$window', '$location', '$timeout', 'toaster', function (MenuService, $scope, $upload, $window, $location, $timeout, toaster) {
        // set menu according to its Name
        MenuService.update("Media");
        $scope.percent = 0;

        // cancel this promise on route change
        var redirectTimeoutPromise;
        // allowed file extensions
        var allowedExts = ["jpg", "jpeg", "png", "gif", "tiff", "svg",
                            "doc", "ppt", "docx", "pptx", "xls", "xlsx", "pages", "numbers", "keynote", "pdf", "pps", "ppsx", "odt", "txt",
                            "mp3", "m4a", "ogg", "wav",
                            "mp4", "m4v", "mkv", "mov", "wmv", "avi", "mpg", "ogv", "3gp", "3g2"];


        $scope.onFileSelect = function ($file) {
            //$files: an array of files selected, each file has name, size, and type.
            var file = $file[0]; // allow only one file a time

            // get alt name
            $scope.altDescription = file.name.replace(/[ \t\r\n\v\f]/g, ' ');

            // get file type
            var name = file.name.split('.');
            var fileType = name[name.length - 1];

            if (!$scope.file.description) {
                toaster.pop('error', null, 'Die Dateibeschreibung muss angegeben werden');
                return;
            }

            // check if filetype is allowed
            if (allowedExts.indexOf(fileType) === -1) {
                toaster.pop('error', null, 'Der Dateityp "' + fileType + '" ist aus Sicherheitsgründen nicht erlaubt');
                return;
            }

            $scope.upload = $upload.upload({
                url: '/api/files', //upload.php script, node.js route, or servlet url
                method: 'POST',
                transformRequest: angular.identity, // Angular’s default transformRequest function will try to serialize our FormData object, so we override it with the identity function to leave the data intact. 
                headers: {'Content-Type': undefined}, //Angular’s default Content-Type header for POST and PUT requests is application/json, so we want to change this, too. By setting ‘Content-Type’: undefined, the browser sets the Content-Type to multipart/form-data for us and fills in the correct boundary. Manually setting ‘Content-Type’: multipart/form-data will fail to fill in the boundary parameter of the request
                // withCredential: true,
                data: {
                    description: $scope.file.description
                },
                file: file,
                // file: $files, //upload multiple files, this feature only works in HTML5 FromData browsers
                /* set file formData name for 'Content-Desposition' header. Default: 'file' */
                //fileFormDataName: myFile, //OR for HTML5 multiple upload only a list: ['name1', 'name2', ...]
                /* customize how data is added to formData. See #40#issuecomment-28612000 for example */
                //formDataAppender: function(formData, key, val){} 
            }).progress(function (evt) {
                var percent = parseInt(100.0 * evt.loaded / evt.total, 10);
                $scope.percent = percent;
                $window.document.getElementsByClassName("progress-bar-percentage")[0].style.width = percent + "%";
            }).success(function () {
                // file has been uploaded successfully
                toaster.pop('success', null, 'Die Datei wurde erfolgreich hochgeladen');
                redirectTimeoutPromise = $timeout(function () {
                    $location.path('/media');
                }, 1000);
            }).error(function (data, status, headers, config) {
                if (status === 409) {
                    toaster.pop('error', null, 'Eine Datei mit dem gleichen Namen exisitiert bereits');
                } else {
                    toaster.pop('error', null, 'Der Upload konnte nicht ausgeführt werden.');
                }
            });
        };

        // cancel redirect promises on route change
        $scope.$on('$locationChangeStart', function () {
            $timeout.cancel(redirectTimeoutPromise);
        });
    }]);