
    'use strict';
    var angular = require('angular');


    angular
        .module('app.types')
        .controller('TypesController', TypesController);

    TypesController.$inject = ['$scope', 'TypesService', 'SweetAlert'];

    /* @ngInject */
    function TypesController($scope, TypesService, SweetAlert) {
        var vm = this;

        vm.types = [];
        vm.mode = 'add';
        vm.addType = addType;
        vm.editType = editType;
        vm.saveType = saveType;
        vm.type = {
            name: '',
            jobname: '',
        };

        vm.deleteType = deleteType;



        /////////////////////////////////////////////////
        activate();

        function activate() {
          getTypes();
        }

        /////////////////////////////////////////////////


        function addType() {
          vm.type = {
              name: '',
              jobname: '',
          };
          vm.mode = 'add';
          openAddModal();
        }

        function editType(type) {
          vm.type = type;
          vm.mode = 'edit';
          openAddModal();
        }


        function openAddModal() {
          $('#typeModal').modal('show');
        }

        function closeAddModal() {
          $('#typeModal').modal('hide');
        }


        function saveType() {
          closeAddModal();
          if (vm.mode == 'add') {
              //add
              TypesService.add(vm.type)
              .then(function(data){
                if (data.status == 200) {
                  vm.types.push(data.data);

                } else {
                  showError('Oops. Something went wrong.');
                }
              });
          } else {
            //edit
            TypesService.edit(vm.type)
            .then(function(data){
              if (data.status != 200) {
                showError('Oops. Something went wrong.');
              }
            });

          }

        }




        function getTypes() {
          TypesService.getAll()
          .then(function(data){
            if (data.status == 200) {
              vm.types = data.data;
            } else {
              showError('Could not load product types.');
            }
          });
        }


        function deleteType(index) {
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
               TypesService.destroy(vm.types[index])
               .then(function(data){
                 if (data.status == 200) {
                   vm.types.splice(index,1);
                 } else {

                   showError("Oops. there was an error.");
                 }
               });
             } else {
                SweetAlert.swal('Cancelled', 'Your type is safe :-)', 'error');
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
