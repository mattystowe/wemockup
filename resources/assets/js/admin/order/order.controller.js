
    'use strict';
    var angular = require('angular');


    angular
        .module('app.order')
        .controller('OrderController', OrderController);

    OrderController.$inject = ['$scope',
                                  '$state',
                                  '$stateParams',
                                  'SweetAlert',
                                  'OrderService'
                                ];

    /* @ngInject */
    function OrderController($scope,
                                $state,
                                $stateParams,
                                SweetAlert,
                                OrderService
                              ) {
        var vm = this;

        vm.order = {};


        /////////////////////////////////////////////////
        activate();



        function activate() {
          loadOrder($stateParams.orderuid);
        }

        /////////////////////////////////////////////////



        function loadOrder(orderuid) {
          OrderService.loadByOrderUID(orderuid)
          .then(function(data){
            if (data.status == 200) {
              vm.order = data.data;
            } else {
              showError('Could not load order');
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
