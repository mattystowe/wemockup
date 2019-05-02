
    'use strict';
    var angular = require('angular');


    angular
        .module('app.orders')
        .controller('OrdersController', OrdersController);

    OrdersController.$inject = ['$scope',
                                  '$state',
                                  'SweetAlert',
                                  'OrderService'
                                ];

    /* @ngInject */
    function OrdersController($scope,
                                $state,
                                SweetAlert,
                                OrderService
                              ) {
        var vm = this;


        vm.page = 1;
        vm.searchquery = '';
        vm.orders = [];

        vm.pagingdata = {};

        vm.nextpage = nextpage;
        vm.previouspage = previouspage;

        vm.searchOrders = searchOrders;
        vm.doSearch = doSearch;

        /////////////////////////////////////////////////
        activate();



        function activate() {
          searchOrders();
        }

        /////////////////////////////////////////////////


        function nextpage() {
          if (vm.pagingdata.current_page < vm.pagingdata.last_page) {
            vm.page = vm.pagingdata.current_page + 1;
            searchOrders();
          }
        }


        function previouspage() {
          if (vm.pagingdata.current_page > 1) {
            vm.page = vm.pagingdata.current_page - 1;
            searchOrders();
          }
        }



        function doSearch() {
          vm.page = 1;
          searchOrders();
        }

        function searchOrders() {
          OrderService.searchOrders(vm.searchquery,vm.page)
          .then(function(data){
            if (data.status == 200) {
              vm.orders = data.data.data;
              vm.pagingdata = data.data;
            } else {
              showError('Could not load orders');
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
