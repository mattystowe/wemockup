
    'use strict';
    var angular = require('angular');


    angular
        .module('app.hosts')
        .controller('HostsController', HostsController);

    HostsController.$inject = ['$scope',
                                  'SweetAlert',
                                  'HostsService'
                                ];

    /* @ngInject */
    function HostsController($scope,
                                SweetAlert,
                                HostsService
                              ) {
        var vm = this;

        vm.hosts = [];

        /////////////////////////////////////////////////
        activate();



        function activate() {
          getHealthyHosts();
        }

        /////////////////////////////////////////////////


        /**
         * Get healthy hosts list
         *
         *
         *
         * @return {[type]} [description]
         */
        function getHealthyHosts() {
          HostsService.gethealthyhosts()
          .then(function(data){
            if (data.status == 200) {
              vm.hosts = data.data;
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
