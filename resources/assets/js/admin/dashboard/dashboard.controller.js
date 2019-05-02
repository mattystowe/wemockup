
    'use strict';
    var angular = require('angular');


    angular
        .module('app.dashboard')
        .controller('DashboardController', DashboardController);

    DashboardController.$inject = ['$scope','ItemService','SweetAlert'];

    /* @ngInject */
    function DashboardController($scope, ItemService, SweetAlert) {
        var vm = this;

        vm.getItemsInProcess = getItemsInProcess;
        vm.itemsInProgress = [];



        /////////////////////////////////////////////////
        activate();

        function activate() {
          getItemsInProcess()
        }

        /////////////////////////////////////////////////


        /**
         * Get items in progress
         *
         *
         * @return {[type]} [description]
         */
        function getItemsInProcess() {
          ItemService.getInProgress()
          .then(function(data){
            if (data.status == 200) {
              vm.itemsInProgress = data.data;
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
