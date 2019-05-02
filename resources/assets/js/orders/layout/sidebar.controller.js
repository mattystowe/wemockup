
    'use strict';
    var angular = require('angular');


    angular
        .module('app.layout')
        .controller('SidebarController', SidebarController);

    SidebarController.$inject = ['$scope','$state'];

    /* @ngInject */
    function SidebarController($scope, $state) {
        var vm = this;

        vm.currentTab = currentTab;

        /////////////////////////////////////////////////
        activate();

        function activate() {
            //console.log($state.current.name);
        }

        /////////////////////////////////////////////////

        function currentTab(tabname) {
          if (tabname == $state.current.name) {
            return 'active';
          } else {
            return '';
          }

        }
    }
