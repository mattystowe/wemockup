
    'use strict';
    var angular = require('angular');


    angular
        .module('app.skus')
        .controller('SkusViewController', SkusViewController);

    SkusViewController.$inject = ['$scope',
                                  '$state',
                                  '$stateParams',
                                  'SweetAlert',
                                  'SkuService',
                                  'PostprocessingService',
                                  'OrderService'
                                ];

    /* @ngInject */
    function SkusViewController($scope,
                                $state,
                                $stateParams,
                                SweetAlert,
                                SkuService,
                                PostprocessingService,
                                OrderService
                              ) {
        var vm = this;

        vm.sku = {};
        vm.availablePostProcs = [];
        vm.newpostproc = {};
        vm.addPostProc = addPostProc;
        vm.saveNewProc = saveNewProc;
        vm.removePostProc = removePostProc;
        vm.procExists = procExists;
        vm.dragControlListeners = {
          orderChanged: orderChanged
        }

        vm.newTestOrder = newTestOrder;
        vm.testorder = {
          email: '',
          firstname: '',
          lastname: ''
        }
        vm.saveTestOrder = saveTestOrder;


        ///////////////////////////////////////////////////
        activate();

        function activate() {
          loadSku($stateParams.skuid);
          loadAvailablePostProcs();
        }

        ////////////////////////////////////////////////////


        function newTestOrder() {
          openTestOrderModal();

        }

        function openTestOrderModal() {
          $('#addTestOrder').modal('show');
        }

        function hideTestOrderModal() {
          $('#addTestOrder').modal('hide');
        }

        function saveTestOrder() {
          hideTestOrderModal();
          OrderService.createTestOrder(vm.sku.id, vm.testorder)
          .then(function(data){
            if (data.status == 200) {
              //order created. Lets go there..
              //
              $state.go('order', {orderuid:data.data.orderuid});
            } else {
              showError('Could not save test order.');
            }
          });
        }



        function orderChanged(event) {
          //console.log('FINISHED DRAGGING');
          var orderValues = [];
          vm.sku.postprocs.forEach(function(item,key){
            orderValues.push({
              'procid': item.id,
              'priority': key
            });
          });

          saveNewProcOrder(orderValues);

        }



        function saveNewProcOrder(orderValues) {
          PostprocessingService.saveOrdering(orderValues, vm.sku)
          .then(function(data){
            if (data.status != 200) {
              showError('Could not save ordering.');
            }
          });
        }



        /**
         * remove post processing stage from sku
         *
         *
         * @param  {[type]} postproc [description]
         * @return {[type]}          [description]
         */
        function removePostProc(postproc, index) {
          PostprocessingService.removeFromSku(postproc.id, vm.sku)
          .then(function(data){
            if (data.status == 200) {
              vm.sku.postprocs.splice(index,1);
            } else {
              showError('Could not add post processing stage.');
            }
          });
        }





        function addPostProc() {
          openPostProcModal();
        }

        function openPostProcModal() {
          $('#addProc').modal('show');
        }

        function closePostProcModal() {
          $('#addProc').modal('hide');
        }





        function procExists(procid) {
          var exists = false;
          vm.sku.postprocs.forEach(function(proc) {
            if (proc.id == procid) {
              exists = true;
            }
          })

          return exists;
        }

        /**
         * Handle adding post processing stage to the sku
         * @return {[type]} [description]
         */
        function saveNewProc() {
          if (procExists(vm.newpostproc.id)) {
            showError('Stage already exists in list.');
          } else {

            closePostProcModal();
            var priority = vm.sku.postprocs.length +1;
            PostprocessingService.addToSku(vm.newpostproc.id, vm.sku, priority)
            .then(function(data){
              if (data.status == 200) {
                vm.sku.postprocs.push(data.data);
              } else {
                showError('Could not add post processing stage.');
              }
            });


          }

        }



        function loadAvailablePostProcs() {
          PostprocessingService.getAll()
          .then(function(data){
            if (data.status == 200) {
              vm.availablePostProcs = data.data;

            } else {
              showError('Could not load post processing stages.');
            }
          });
        }


        function loadSku(id) {
          SkuService.load(id)
          .then(function(data){
            if (data.status == 200) {
              vm.sku = data.data;

            } else {
              showError('Could not load sku.');
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
