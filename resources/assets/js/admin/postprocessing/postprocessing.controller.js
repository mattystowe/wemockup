
    'use strict';
    var angular = require('angular');


    angular
        .module('app.postprocessing')
        .controller('PostprocessingController', PostprocessingController);

    PostprocessingController.$inject = ['$scope', 'PostprocessingService', 'SweetAlert'];

    /* @ngInject */
    function PostprocessingController($scope, PostprocessingService, SweetAlert) {
        var vm = this;

        vm.steps = [];
        vm.step = {
          name: '',
          jobname: '',
          data: '',
        };
        vm.addStep = addStep;
        vm.editStep = editStep;
        vm.saveStep = saveStep;
        vm.deleteStep = deleteStep;

        /////////////////////////////////////////////////
        activate();

        function activate() {
          getSteps();
        }

        /////////////////////////////////////////////////

        function getSteps() {
          PostprocessingService.getAll()
          .then(function(data){
            if (data.status == 200) {
              vm.steps = data.data;
            } else {
              showError('Oops. Something went wrong.');
            }
          });
        }



        function addStep() {
          vm.step = {
            name: '',
            jobname: '',
            data: '',
          };

          vm.mode = 'add';
          openAddModal();
        }


        function editStep(step) {
          vm.step = step;
          vm.mode = 'edit';
          openAddModal();
        }

        function openAddModal() {
          $('#stepModal').modal('show');
        }

        function closeAddModal() {
          $('#stepModal').modal('hide');
        }



                function saveStep() {
                  closeAddModal();
                  if (vm.mode == 'add') {
                      //add
                      PostprocessingService.add(vm.step)
                      .then(function(data){
                        if (data.status == 200) {
                          vm.steps.push(data.data);

                        } else {
                          showError('Oops. Something went wrong.');
                        }
                      });
                  } else {
                    //edit
                    //add
                    PostprocessingService.edit(vm.step)
                    .then(function(data){
                      if (data.status != 200) {
                        showError('Oops. Something went wrong.');
                      }
                    });

                  }

                }

                function deleteStep(index) {
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
                       PostprocessingService.destroy(vm.steps[index])
                       .then(function(data){
                         if (data.status == 200) {
                           vm.steps.splice(index,1);
                         } else {
                           //
                           //error
                           //
                           showError("Oops. there was an error.");
                         }
                       });
                     } else {
                        SweetAlert.swal('Cancelled', 'Your frame step is safe :-)', 'error');
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
