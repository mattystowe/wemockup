
    'use strict';
    var angular = require('angular');


    angular
        .module('app.categories')
        .controller('FrameConfigurationsController', FrameConfigurationsController);

    FrameConfigurationsController.$inject = ['$scope', 'FrameConfigurationsService', 'SweetAlert'];

    /* @ngInject */
    function FrameConfigurationsController($scope, FrameConfigurationsService, SweetAlert) {
        var vm = this;

        vm.frameconfigs = [];
        vm.frameconfig = {
          name: '',
          dimx: '',
          dimy: '',
          outputformat: 'png',
          watermark: false
        };

        vm.addConfig = addConfig;
        vm.saveConfig = saveConfig;
        vm.editConfig = editConfig;
        vm.mode = 'add';
        vm.deleteConfig = deleteConfig;

        /////////////////////////////////////////////////
        activate();

        function activate() {
          loadframeconfigs();
        }

        /////////////////////////////////////////////////




        function addConfig() {
          vm.frameconfig = {
            name: '',
            dimx: '',
            dimy: '',
            outputformat: 'png',
            watermark: false
          };

          vm.mode = 'add';
          openAddModal();
        }


        function editConfig(frameconfig) {
          vm.frameconfig = frameconfig;
          vm.mode = 'edit';
          openAddModal();
        }

        function openAddModal() {
          $('#configModal').modal('show');
        }

        function closeAddModal() {
          $('#configModal').modal('hide');
        }



                function saveConfig() {
                  closeAddModal();
                  if (vm.mode == 'add') {
                      //add
                      FrameConfigurationsService.add(vm.frameconfig)
                      .then(function(data){
                        if (data.status == 200) {
                          vm.frameconfigs.push(data.data);

                        } else {
                          showError('Oops. Something went wrong.');
                        }
                      });
                  } else {
                    //edit
                    //add
                    FrameConfigurationsService.edit(vm.frameconfig)
                    .then(function(data){
                      if (data.status != 200) {
                        showError('Oops. Something went wrong.');
                      }
                    });

                  }

                }




        function loadframeconfigs() {
          FrameConfigurationsService.getAll()
          .then(function(data){
            if (data.status == 200) {
              vm.frameconfigs = data.data;
            } else {
              showError('Oops. Something went wrong.');
            }
          });
        }




        function deleteConfig(index) {
          SweetAlert.swal({
             title: 'Are you sure?',
             text: '',
             type: 'warning',
             showCancelButton: true,
             confirmButtonColor: '#DD6B55',confirmButtonText: 'Yes, delete it!',
             cancelButtonText: 'No, cancel!',
             closeOnConfirm: true,
             closeOnCancel: false },
          function(isConfirm){
             if (isConfirm) {
               FrameConfigurationsService.destroy(vm.frameconfigs[index])
               .then(function(data){
                 if (data.status == 200) {
                   vm.frameconfigs.splice(index,1);
                 } else {
                   //
                   //error
                   //
                   showError("Oops. there was an error.");
                 }
               });
             } else {
                SweetAlert.swal('Cancelled', 'Your frame config is safe :-)', 'error');
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
