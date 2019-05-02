
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


        vm.itemjobchartlabels = [];
        vm.itemjobchartdata = [];


        vm.itemjobs = [];
        vm.itemjobspaging = {};
        vm.page = 1;
        vm.nextpage = nextpage;
        vm.previouspage = previouspage;

        vm.getstatusclass = getstatusclass;


        vm.viewItemLog = viewItemLog;
        vm.viewPostprocLog = viewPostprocLog;

        vm.itempostprocs = [];

        vm.cancel = cancel;
        vm.showCancelButton = showCancelButton;

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



        function showCancelButton() {
          switch (vm.item.status) {
            case 'PENDINGSETUP':
              return false;
              break;
              case 'QUEUED':
                return true;
                break;
                case 'PROCESSING':
                  return true;
                  break;
                  case 'FINISHING':
                    return true;
                    break;
                    case 'COMPLETE':
                      return true;
                      break;
                      case 'FAILED':
                        return false;
                        break;
                        case 'CANCELLED':
                          return false;
                          break;
            default:

          }
        }

        /**
         * Make the call to cancel a job
         *
         *
         *
         * @return {[type]} [description]
         */
        function cancel() {
          ItemService.cancel(vm.item.itemuid, 'Admin cancelled')
          .then(function(data){
            if (data.status == 200) {
              if (data.data != 'notfound') {
                pollForUpdates();
              } else {
                showError('Oops. Something went wrong.');
              }
            } else {
              showError('Oops. Something went wrong.');
            }
          });
        }






        /**
         * Handle the viewing of an ItemJob log
         *
         *
         * @param  {[type]} itemid [description]
         * @return {[type]}        [description]
         */
        function viewItemLog(itemjobid) {
          $state.go('itemlog',{itemjobid: itemjobid});
        }


        /**
         * Handle the viewing of a postproc log
         *
         *
         * @param  {[type]} procid [description]
         * @return {[type]}        [description]
         */
        function viewPostprocLog(procid) {
          $state.go('itempostproclog',{itempostprocid: procid});
        }






        function getstatusclass(status) {
          switch (status) {
            case 'QUEUED':
              return 'label label-default';
              break;
              case 'PROCESSING':
                return 'label label-primary';
                break;
                case 'COMPLETE':
                  return 'label label-success';
                  break;
                  case 'FAILED':
                    return 'label label-danger';
                    break;
                    case 'ABORTED':
                      return 'label label-warning';
                      break;
            default:
            return 'label label-default';
            break;
          }
        }




        function nextpage() {
          if (vm.itemjobspaging.current_page < vm.itemjobspaging.last_page) {
            vm.page = vm.itemjobspaging.current_page + 1;
            loadItemJobs($stateParams.itemuid);
          }
        }


        function previouspage() {
          if (vm.itemjobspaging.current_page > 1) {
            vm.page = vm.itemjobspaging.current_page - 1;
            loadItemJobs($stateParams.itemuid);
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
              return'/admin/item/partials/setup.html';
            case 'QUEUED':
              return'/admin/item/partials/processing.html';
            case 'PROCESSING':
              return'/admin/item/partials/processing.html';
            case 'FINISHING':
              return'/admin/item/partials/finishing.html';
            case 'COMPLETE':
              return'/admin/item/partials/complete.html';
              break;
            case 'FAILED':
              return'/admin/item/partials/failed.html';
              break;
            case 'CANCELLED':
              return'/admin/item/partials/cancelled.html';
              break;
            default:
            return '/admin/item/partials/blank.html';

          }
        }

        function loadItem(itemuid) {
          ItemService.loadByItemUID(itemuid)
          .then(function(data){
            if (data.status == 200) {
              if (data.data != 'notfound') {
                vm.item = data.data;

                






                //Get itempostprocs
                loadItemPostProcs(itemuid);



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






        function loadItemPostProcs(itemuid) {
          ItemService.loadItemPostProcs(itemuid)
          .then(function(data){
            if (data.status == 200) {
              if (data.data != 'notfound') {
                vm.itempostprocs = data.data;
              } else {
                showError('Oops. Something went wrong.');
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
