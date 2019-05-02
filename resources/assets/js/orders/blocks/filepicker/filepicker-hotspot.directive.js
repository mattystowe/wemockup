(function() {
    'use strict';

    var filepicker = require('filepicker-js');

    var angular = require('angular');
    angular
        .module('blocks.filepicker')
        .directive('filepickerhotspot', filepickerhotspot);

    /* @ngInject */
    function filepickerhotspot() {
        var directive = {
            restrict: 'E',
            templateUrl: 'app/blocks/filepicker/partials/hotspot.html',
            scope: {
              'id': '@', // the id to set for the drop panel
              'debug': '@', // false by default.  If true picker just returns dummy data straight away
              callback: '&', // the callback function that filepicker calls on finished
              'multiple': '@', // can pick multiple files true | false
              'maxfiles': '@', // Maximum files allowed to be picked at 1 time.
              'folders': '@', // can users drop whole folders true | false
              'mimetype': '@', // eg image/* for all images, or  application/msword Word docs
              'mimetypes': '@', // comma list of types eg image/*,text/*
              'extension': '@', // eg .pdf
              'extensions': '@', // comma list of file ext eg .png,.pdf,.js
              'maxsize': '@', //bytes maximum - default unlimited
              'service': '@', // eg COMPUTER
              'services': '@', // Comma list COMPUTER,FACEBOOK,BOX
              'backgroundupload': '@', // allow background upload.  default false;
              'location': '@', //'S3', 'azure', 'dropbox', 'rackspace' and 'gcs' - Defaults to azure
              'path': '@', // path to store the file
              'access': '@' // public or private by default
            },
            link: linkFunc,
            controller: filePickerController,
            controllerAs: 'vm',
            bindToController: true
        };

        return directive;

        function linkFunc(scope, el, attr, ctrl) {

        }
    }




    filePickerController.$inject = ['$scope'];

    function filePickerController($scope) {

      var vm = this;



      vm.activate = activate;
      vm.configurefiletypes = configurefiletypes;
      vm.configurefileextensions = configurefileextensions;
      vm.configureBehaviour = configureBehaviour;
      vm.setupDropPanel = setupDropPanel;

      vm.targetId = '#' + vm.id;
      vm.apikey = 'AQZxVZBoYSAesdUTJ6W1Jz';

      vm.startProgress = startProgress;
      vm.stopProgress = stopProgress;
      vm.resetProgress = resetProgress;

      vm.fperror = false;
      vm.fperrormsg = '';
      vm.showError = showError;
      vm.clearError = clearError;

      vm.progress = {
        percentage: 0,
        uploading: false,
        defaultdisplaytext: 'Drop files here',
        displaytext: 'Drop files here',
        panelClass: 'fphotspot'
      };

      /////////////////////////////////////
      //Picker options
      vm.pickeroptions = {
        debug: vm.debug ? vm.debug : false,
        maxFiles: vm.maxfiles,
        multiple: vm.multiple ? vm.multiple : true,
        folders: vm.folders ? vm.folders : true,
        maxSize: vm.maxsize ? vm.maxsize : '',
        backgroundUpload: vm.backgroundupload ? vm.backgroundupload : true,
        location: vm.location ? vm.location : 'azure',
        path: vm.path ? vm.path : '',
        access: vm.access ? vm.access : 'private'
      };



      //////////////////////////////////////

      function activate() {
        vm.configurefiletypes();
        vm.configurefileextensions();
        vm.configureBehaviour();
        vm.setupDropPanel();
      }


      vm.activate();


      function configurefiletypes() {
        if (vm.mimetypes) {
          vm.pickeroptions.mimetypes = vm.mimetypes.split(',');
        } else {
          vm.pickeroptions.mimetypes = '';
        }
      }

      function configurefileextensions() {
        if (vm.extensions) {
          vm.pickeroptions.extensions = vm.extensions.split(',');
        } else {
          vm.pickeroptions.extensions = '';
        }
      }

      //////////////////////////////////////////////////
      //Picker Behaviour
      function configureBehaviour() {

          vm.pickeroptions.dragEnter = function(){
            //console.log('drag enter');
            $scope.$apply(function () {
              vm.progress.panelClass = 'fphotspot-enter';
              vm.progress.displaytext = 'Drop to upload';
            });
          };

          vm.pickeroptions.dragLeave = function(){
            //console.log('drag leave');
            $scope.$apply(function () {
              vm.progress.displaytext = vm.progress.defaultdisplaytext;
              vm.progress.panelClass = 'fphotspot';
            });

          };

          vm.pickeroptions.onStart = function() {
            //console.log('started upload');
            $scope.$apply(function () {
              vm.clearError();
              vm.progress.displaytext = 'Uploading';
              vm.progress.panelClass = 'fphotspot';
              vm.startProgress();
            });
          };

          vm.pickeroptions.onSuccess = function(Blobs) {
            //console.log(Blobs);
            $scope.$apply(function () {
              vm.progress.displaytext = vm.progress.defaultdisplaytext;
              vm.progress.panelClass = 'fphotspot';
              vm.stopProgress();
              vm.resetProgress();
              vm.callback({file: Blobs});
            });
          };

          vm.pickeroptions.onProgress = function(percentage) {
            //console.log('uploading - %' + percentage);
            $scope.$apply(function () {
              vm.progress.percentage = percentage;
            });
          };

          vm.pickeroptions.onError = function(type, message) {
            //console.log('Error - ' + message);
            $scope.$apply(function () {
              vm.showError(message);
              vm.progress.panelClass = 'fphotspot';
              vm.stopProgress();
              vm.resetProgress();
            });
          };

      }



      function startProgress() {
        vm.progress.uploading = true;
      }

      function stopProgress() {
        vm.progress.uploading = false;
      }

      function resetProgress() {
        vm.progress.percentage = 0;
      }



      ///////////////////////////////////////
      //filepicker execute
      function setupDropPanel() {
          filepicker.setKey(vm.apikey);
          filepicker.makeDropPane($(vm.targetId)[0], vm.pickeroptions);
      }



      //Show error
      function showError(msg) {
        vm.fperror = true;
        vm.fperrormsg = msg;
      }

      function clearError() {
        vm.fperror = false;
        vm.fperrormsg = '';
      }

    }
})();
