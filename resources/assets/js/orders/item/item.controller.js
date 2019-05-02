
    'use strict';
    var angular = require('angular');


    angular
        .module('app.item')
        .controller('ItemController', ItemController);

    ItemController.$inject = ['$scope',
                                  '$stateParams',
                                  'SweetAlert',
                                  'ItemService',
                                  '$interval',
                                  '$state'
                                ];

    /* @ngInject */
    function ItemController($scope,
                                $stateParams,
                                SweetAlert,
                                ItemService,
                                $interval,
                                $state
                              ) {
        var vm = this;

        vm.item = {
          status: 'blank'
        };
        vm.getStageInclude = getStageInclude;
        vm.FileUploaded = FileUploaded;

        vm.storageLocation = {
          'S3': 'https://s3-eu-west-1.amazonaws.com/wemockupstorage/'
        };

        vm.submitValid = submitValid;
        vm.submitItem = submitItem;

        vm.resubmit = resubmit;

        vm.submitting = false;

        vm.pollingPromise = null;
        vm.polling = false;

        vm.filestackprocessingurl = 'https://process.filestackapi.com/ALTm0uWhxTzKUZWuy6VCrz/';
        vm.getResponsiveFileUrl = getResponsiveFileUrl;


        vm.isImage = isImage;


        ///////////////////////////////////////////////////
        activate();

        function activate() {
          loadItem($stateParams.itemuid);

        }

        ////////////////////////////////////////////////////





        /**
         * Returns bool for whether a filename is an image or not for display purposes only.
         *
         *
         *
         * @param  {[type]}  filename [description]
         * @return {Boolean}          [description]
         */
        function isImage(filename) {
          if (filename.includes('png')) {
            return true;
          } else {
            return false;
          }
        }



        function startUpdatePolling() {
          if (vm.polling == false) {
            vm.pollingPromise = $interval(pollForUpdates, 5000); // 5 seconds update
            vm.polling = true;
          }
        }

        function pollForUpdates() {
          loadItem($stateParams.itemuid);
        }



        function submitValid() {
          var valid = true;
          vm.item.sku.product.inputoptions.forEach(function(inputoption) {
            if (!inputoption.value) {
              valid = false;
            }
          });

          if (vm.submitting) {
            valid = false;
          }

          return valid;
        }





        /**
         * Handle submission of the item
         *
         *
         *
         * @return {[type]} [description]
         */
        function submitItem() {
          SweetAlert.swal({
             title: 'Are you ready?',
             text: '',
             type: 'warning',
             showCancelButton: true,
             confirmButtonColor: '#DD6B55',confirmButtonText: 'Yes, Lets Go!',
             cancelButtonText: 'No, cancel!',
             closeOnConfirm: true,
             closeOnCancel: true },
          function(isConfirm){
             if (isConfirm) {
               sendForProcessing();
             }
          });
        }


        /**
         * Handle submission of the item
         *
         *
         *
         * @return {[type]} [description]
         */
        function resubmit() {
          SweetAlert.swal({
             title: 'Are you sure you wish to re-submit for processing?',
             text: '',
             type: 'warning',
             showCancelButton: true,
             confirmButtonColor: '#DD6B55',confirmButtonText: 'Yes, Lets Go!',
             cancelButtonText: 'No, cancel!',
             closeOnConfirm: true,
             closeOnCancel: true },
          function(isConfirm){
             if (isConfirm) {
               sendForProcessingAgain();
             }
          });
        }





        function sendForProcessing() {
          vm.submitting = true; // set flag
          ItemService.submitForProcessing(vm.item.itemuid, vm.item.sku.product.inputoptions)
          .then(function(data){
            console.log(data);
            vm.submitting = false; // set flag
            if (data.status == 200) {
              if (data.data != 'error') {
                vm.item.status = 'QUEUED';
                startUpdatePolling();
              } else {

                showError('Oops. Something went wrong.  Please check you have filled everything in.');
              }

            } else {
              showError('Oops. Something went wrong.');
            }

          });
        }


        function sendForProcessingAgain() {
          vm.submitting = true; // set flag
          ItemService.submitForProcessingAgain(vm.item.itemuid)
          .then(function(data){
            console.log(data);
            vm.submitting = false; // set flag
            if (data.status == 200) {
              if (data.data != 'error') {
                vm.item.status = 'QUEUED';
                startUpdatePolling();
              } else {

                showError('Oops. Something went wrong.  Please check you have filled everything in.');
              }

            } else {
              showError('Oops. Something went wrong.');
            }

          });
        }






        /**
         * Process file uploads and update inputoption value
         *
         *
         *
         * @param {[type]} files [description]
         * @param {[type]} index [description]
         */
        function FileUploaded(files, index) {
          //console.log(files[0]);
          vm.item.sku.product.inputoptions[index].value  = vm.storageLocation.S3 + files[0].key;
          vm.item.sku.product.inputoptions[index].filename = files[0].filename;
          vm.item.sku.product.inputoptions[index].filekey = files[0].key;
          vm.item.sku.product.inputoptions[index].filestackurl = files[0].url;


          //set display option
          vm.item.sku.product.inputoptions[index].fileuploaded = files[0].filename;
        }


        /**
         * Get a responsive image url
         *
         *
         *
         * @param  {[type]} url       [description]
         * @param  {[type]} maxwidth  [description]
         * @param  {[type]} maxheight [description]
         * @return {[type]}           [description]
         */
        function getResponsiveFileUrl(url,maxwidth,maxheight) {
          return vm.filestackprocessingurl + 'resize=width:' + maxwidth + ',height:' + maxheight + ',fit:clip/' + url;
        }


        /**
         * Return the include path for the current item stage display
         *
         *
         *
         * @return {[type]} [description]
         */
        function getStageInclude() {
          switch (vm.item.status) {
            case 'PENDINGSETUP':
              return'/orders/item/partials/setup.html';
            case 'QUEUED':
              return'/orders/item/partials/processing.html';
            case 'PROCESSING':
              return'/orders/item/partials/processing.html';
            case 'FINISHING':
              return'/orders/item/partials/finishing.html';
            case 'COMPLETE':
              return'/orders/item/partials/complete.html';
              break;
            case 'FAILED':
              return'/orders/item/partials/failed.html';
              break;
            case 'CANCELLED':
              return'/orders/item/partials/cancelled.html';
              break;
            default:
            return '/orders/item/partials/blank.html';

          }
        }

        function loadItem(itemuid) {
          ItemService.loadByItemUID(itemuid)
          .then(function(data){
            if (data.status == 200) {
              if (data.data != 'notfound') {
                vm.item = data.data;
                //start polling for updates if processing or finishing
                if (vm.item.status == 'QUEUED' || vm.item.status == 'PROCESSING' || vm.item.status == 'FINISHING') {
                  startUpdatePolling();
                  $scope.$on('$destroy', function () {
                    $interval.cancel(vm.pollingPromise); // remove the polling if leaving page
                  });
                }
                if (vm.item.status == 'COMPLETE' || vm.item.status == 'FAILED' || vm.item.status == 'CANCELLED') {
                  $interval.cancel(vm.pollingPromise);
                }
              } else {
                $state.go('404');
              }

            } else {
              showError('Oops. Something went wrong.');
            }
          });
        }



        /**
        * Show an error dialog with a message
        *
        *
        *
        * @param  {[type]} msg [description]
        * @return {[type]}     [description]
        */
       function showError(msg) {
         SweetAlert.swal({
            title: 'Error',
            text: msg,
            type: 'warning',
            showCancelButton: false,
            confirmButtonColor: '#DD6B55',confirmButtonText: 'Ok',
            closeOnConfirm: true
         });
       }

    }
