'use strict';

function MediaController(FileService) {
    var vm = this;

    vm.files = [];

    activate();

    function activate() {
        FileService.getFiles()
            .then(function (data) {
                vm.files = data;
            });
    }

}

function MediaAddController(FileService, Upload, $scope) {
    var vm = this;

    // holds all selected files (from drop zone or select button)
    vm.files = [];
    vm.fileDescription = '';
    vm.percent = [];

    $scope.$watch('vm.files', function () {
        vm.upload(vm.files);
    });

    vm.upload = function (files) {
        uploadFiles(files);
    };

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

function MediaDetailController(FileService, $routeParams) {
    var vm = this,
        fileId = $routeParams.id;

    vm.file = {};

    activate();

    function activate() {
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
    MediaAddController.$inject = ['FileService', 'Upload', '$scope'];
    MediaDetailController.$inject = ['FileService', '$routeParams'];

})(angular);

