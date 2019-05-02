
    'use strict';
    var angular = require('angular');


    angular
        .module('app.categories')
        .controller('CategoriesController', CategoriesController);

    CategoriesController.$inject = ['$scope', 'CategoriesService', 'SweetAlert'];

    /* @ngInject */
    function CategoriesController($scope, CategoriesService, SweetAlert) {
        var vm = this;

        vm.categories = [];
        vm.deleteCategory = deleteCategory;
        vm.addCategory = addCategory;
        vm.editCategory = editCategory;
        vm.saveCategory = saveCategory;
        vm.category = {
          name: ''
        };
        vm.mode = 'add';


        /////////////////////////////////////////////////
        activate();

        function activate() {
          loadcategories();
        }

        /////////////////////////////////////////////////


        function loadcategories() {
          CategoriesService.getAll()
          .then(function(data){
            if (data.status == 200) {
              vm.categories = data.data;
            } else {
              showError('Oops. Something went wrong.');
            }
          });
        }


        function deleteCategory(index) {
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
               CategoriesService.destroy(vm.categories[index])
               .then(function(data){
                 if (data.status == 200) {
                   vm.categories.splice(index,1);
                 } else {
                   //
                   //error
                   //
                   showError("Oops. there was an error.");
                 }
               });
             } else {
                SweetAlert.swal('Cancelled', 'Your category is safe :-)', 'error');
             }
          });




        }



        function addCategory() {
          vm.category = {
            name: ''
          };
          vm.mode = 'add';
          openAddModal();
        }

        function editCategory(category) {
          vm.category = category;
          vm.mode = 'edit';
          openAddModal();
        }

        function openAddModal() {
          $('#categoryModal').modal('show');
        }

        function closeAddModal() {
          $('#categoryModal').modal('hide');
        }




        function saveCategory() {
          closeAddModal();
          if (vm.mode == 'add') {
              //add
              CategoriesService.add(vm.category)
              .then(function(data){
                if (data.status == 200) {
                  vm.categories.push(data.data);

                } else {
                  showError('Oops. Something went wrong.');
                }
              });
          } else {
            //edit
            //add
            CategoriesService.edit(vm.category)
            .then(function(data){
              if (data.status != 200) {
                showError('Oops. Something went wrong.');
              }
            });

          }

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
