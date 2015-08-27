'use strict';

function MediaController(FileService) {
    var vm = this;

    vm.files = [];

    activate();

    function activate() {
        FileService.getFiles()
            .then(function (data) {
                // we do have to make some filtering for the thumbnail
                // @see http://stackoverflow.com/questions/16507040/angular-filter-works-but-causes-10-digest-iterations-reached

                for (var i=0; i<data.length; i++) {
                    if (data.hasOwnProperty(i)) {

                        if (data[i].link.match('(.jpg|.png|.jpeg|.gif|.bmp)')) {
                            data[i]._preview = '<img src="' + data[i].thumbnail_link + '" width="40">';
                        } else if (data[i].link.match('.pdf')) {
                            data[i]._preview = '<i class="fa fa-file-pdf-o"></i>';
                        } else if (data[i].link.match('(.mp3|.m4a|.aac)')) {
                            data[i]._preview = '<i class="fa fa-file-audio-o"></i>';
                        } else if (data[i].link.match('(.mp4|.mpeg)')) {
                            data[i]._preview = '<i class="fa fa-file-video-o></i>';
                        } else if (data[i].link.match('(.zip|.tar)')) {
                            data[i]._preview = '<i class="fa fa-file-archive-o"></i>';
                        }
                    }
                }

                vm.files = data;

            });
    }

}

function MediaAddController(FileService, Upload, NavigationService, $location, $scope) {
    var vm = this;

    // holds all selected files (from drop zone or select button)
    vm.files = [];
    vm.fileDescription = '';
    vm.percent = [];

    activate();

    $scope.$watch('vm.files', function () {
        vm.upload(vm.files);
    });

    vm.upload = function (files) {
        uploadFiles(files);
    };

    function activate () {
        vm.backgroundColorClass = NavigationService.getBackgroundColorClass($location.path());
    }

    function uploadFiles(files) {
        if (files && files.length) {
            for (var i = 0; i < files.length; i++) {
                var file = files[i];
                if (!file.$error) {
                    Upload.upload({
                        url: '/api/files',
                        method: 'POST',
                        fields: {
                            "description": vm.fileDescription
                        },
                        file: file
                    }).progress(function (evt) {
                        var progressPercentage = parseInt(100.0 * evt.loaded / evt.total);
                        console.log('progress: ' + progressPercentage + '% ' + evt.config.file.name);
                        vm.percent[evt.config.file.name] = progressPercentage;
                    }).success(function (data, status, headers, config) {
                        console.log('file: ' + config.file.name + ', Response: ' + JSON.stringify(data))
                    });
                }
            }
        }
    }
}

function MediaDetailController(FileService, NavigationService, $location, $routeParams) {
    var vm = this,
        fileId = $routeParams.id;

    vm.file = {};

    activate();

    function activate() {
        vm.backgroundColorClass = NavigationService.getBackgroundColorClass($location.path());
        FileService.getFile(fileId)
            .then(function (data) {
                vm.file = data;
            });
    }
}

(function (angular) {
    angular
        .module('cms.controllers')
        .controller('MediaController', MediaController)
        .controller('MediaAddController', MediaAddController)
        .controller('MediaDetailController', MediaDetailController);

    MediaController.$inject = ['FileService'];
    MediaAddController.$inject = ['FileService', 'Upload', 'NavigationService', '$location', '$scope'];
    MediaDetailController.$inject = ['FileService', 'NavigationService', '$location', '$routeParams'];

})(angular);

