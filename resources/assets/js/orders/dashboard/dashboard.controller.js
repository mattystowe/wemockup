
    'use strict';
    var angular = require('angular');


    angular
        .module('app.dashboard')
        .controller('DashboardController', DashboardController);

    DashboardController.$inject = ['$scope',
                                  '$stateParams',
                                  'SweetAlert',
                                  'OrderService',
                                  '$state'
                                ];

    /* @ngInject */
    function DashboardController($scope,
                                $stateParams,
                                SweetAlert,
                                OrderService,
                                $state
                              ) {
        var vm = this;

        vm.order = {};



        ///////////////////////////////////////////////////
        activate();

        function activate() {
          loadOrder($stateParams.orderuid);

        }

        ////////////////////////////////////////////////////


        function loadOrder(orderuid) {
          OrderService.loadByOrderUID(orderuid)
          .then(function(data){
            if (data.status == 200) {
              if (data.data != 'notfound') {
                vm.order = data.data;
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
