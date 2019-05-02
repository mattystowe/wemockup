
    'use strict';
    var angular = require('angular');


    angular
        .module('app.products')
        .controller('ProductsViewController', ProductsViewController);

    ProductsViewController.$inject = ['$scope',
                                  '$stateParams',
                                  'ProductsService',
                                  'SweetAlert',
                                  'TypesService',
                                  'FrameConfigurationsService',
                                  'InputTypesService',
                                  'CategoriesService'
                                ];

    /* @ngInject */
    function ProductsViewController($scope,
                                $stateParams,
                                ProductsService,
                                SweetAlert,
                                TypesService,
                                FrameConfigurationsService,
                                InputTypesService,
                                CategoriesService
                              ) {
        var vm = this;

        vm.product = {};
        vm.sku = {};
        vm.frameconfigs = [];
        vm.inputoption = {
        };


        vm.mode = 'add';
        vm.addSku = addSku;
        vm.editSku = editSku;
        vm.saveSku = saveSku;
        vm.deleteSku = deleteSku;

        vm.addInputOption = addInputOption;
        vm.inputImageUploaded = inputImageUploaded;
        vm.productImageUploaded = productImageUploaded;
        vm.productFullImageUploaded = productFullImageUploaded;
        vm.storageLocation = {
          'S3': 'https://s3-eu-west-1.amazonaws.com/wemockupstorage/'
        };
        vm.saveInputOption = saveInputOption;
        vm.editInputOption = editInputOption;
        vm.mode = 'add';

        vm.deleteInputOption = deleteInputOption;

        vm.inputTypeSelected = inputTypeSelected;

        vm.dragControlListeners = {
          orderChanged: orderChanged
        }


        vm.editProduct = editProduct;
        vm.saveProduct = saveProduct;


        ///////////////////////////////////////////////////
        activate();

        function activate() {
          loadProduct($stateParams.productid);
          loadframeconfigs();
          getAvailableTypes();
          getAvailableCategories();
        }

        ////////////////////////////////////////////////////





        function deleteSku(sku, index) {
          ProductsService.deleteSku(sku)
          .then(function(data){
            if (data.status == 200) {
              vm.product.skus.splice(index,1);
            } else {
              showError('Could not delete sku.');
            }
          });
        }


        function getAvailableTypes() {
          TypesService.getAll()
          .then(function(data){
            if (data.status == 200) {
              vm.availableTypes = data.data;
            } else {
              showError('Could not load product types.');
            }
          });
        }

        function getAvailableCategories() {
          CategoriesService.getAll()
          .then(function(data){
            if (data.status == 200) {
              vm.availableCategories = data.data;
            } else {
              showError('Oops. Something went wrong loading categories.');
            }
          });
        }



        function editProduct() {
          openEditModal();
        }

        function openEditModal() {
          $('#editModal').modal('show');
        }

        function closeEditModal() {
          $('#editModal').modal('hide');
        }


        function saveProduct() {
          closeEditModal();
          ProductsService.saveProduct(vm.product)
          .then(function(data){
            if (data.status != 200) {
              showError('Could not save product.');
            }
          });
        }






                function orderChanged(event) {
                  //console.log('FINISHED DRAGGING');
                  var orderValues = [];
                  vm.product.inputoptions.forEach(function(item,key){
                    orderValues.push({
                      'inputoptionid': item.id,
                      'priority': key
                    });
                  });

                  saveNewInputOrder(orderValues);

                }

                function saveNewInputOrder(orderValues) {
                  ProductsService.saveInputOrdering(orderValues, vm.product)
                  .then(function(data){
                    if (data.status != 200) {
                      showError('Could not save ordering.');
                    }
                  });
                }





        function inputTypeSelected() {
          vm.inputoption.data = InputTypesService.getdatatemplate(vm.inputoption.input_type);
        }


        /**
         * Handle image uploaded event from filepicker for the new product.
         *
         *
         *
         * @param  {[type]} file [description]
         * @return {[type]}      [description]
         */
        function productImageUploaded(files) {
          //console.log(files);
          vm.product.image = vm.storageLocation.S3 + files[0].key;
        }

        /**
         * Handle full image uploaded event from filepicker for the new product.
         *
         *
         *
         * @param  {[type]} file [description]
         * @return {[type]}      [description]
         */
        function productFullImageUploaded(files) {
          console.log(files);
          vm.product.fullimage = vm.storageLocation.S3 + files[0].key;
        }



        function deleteInputOption(inputoption, index) {
          ProductsService.deleteInputOption(inputoption)
          .then(function(data){
            if (data.status == 200) {
              vm.product.inputoptions.splice(index,1);
            } else {
              showError('Could not add delete option.');
            }
          });
        }



        function inputImageUploaded(files) {
          console.log(files);
          vm.inputoption.image = vm.storageLocation.S3 + files[0].key;
        }

        function addInputOption() {
          vm.mode = 'add';
          openInputModal();
        }

        function editInputOption(inputoption) {
          vm.inputoption = inputoption;
          vm.mode = 'edit';
          openInputModal();
        }


        function openInputModal() {
          $('#inputModal').modal('show');
        }

        function closeInputModal() {
          $('#inputModal').modal('hide');
        }


        function saveInputOption() {
          closeInputModal();
          if (vm.mode == 'add') {
            //add
            vm.inputoption.priority = vm.product.inputoptions.length + 1;
            ProductsService.addInputOption(vm.inputoption, vm.product)
            .then(function(data){
              if (data.status == 200) {
                vm.product.inputoptions.push(data.data);

              } else {
                showError('Could not add input option.');
              }
            });

          } else {
            //edit
            ProductsService.editInputOption(vm.inputoption)
            .then(function(data){
              if (data.status != 200) {
                showError('Could not add input option.');
              }
            });
          }
        }


        function loadframeconfigs() {
          FrameConfigurationsService.getAll()
          .then(function(data){
            if (data.status == 200) {
              vm.frameconfigs = data.data;
            } else {
              showError('Oops. Something went wrong loading frame configurations.');
            }
          });
        }

        function addSku() {
          vm.sku = {};
          vm.mode = 'add';
          openSkuModal();
        }

        function editSku(sku) {
          vm.sku = sku;
          vm.mode = 'edit';
          openSkuModal();
        }



        function openSkuModal() {
          $('#skuModal').modal('show');
        }

        function closeAddModal() {
          $('#skuModal').modal('hide');
        }



        function saveSku() {
          closeAddModal();
          if (vm.mode == 'add') {
              //add
              vm.sku.product_id = vm.product.id;
              ProductsService.addSku(vm.sku)
              .then(function(data){
                if (data.status == 200) {
                  vm.product.skus.push(data.data);

                } else {
                  showError('Could not add SKU.');
                }
              });
          } else {
            //edit
            vm.sku.product_id = vm.product.id;
            ProductsService.editSku(vm.sku)
            .then(function(data){
              if (data.status != 200) {
                showError('Could not update SKU.');
              }
            });
          }

        }


        function loadProduct(id) {
          ProductsService.loadProduct(id)
          .then(function(data){
            if (data.status == 200) {
              vm.product = data.data;
              //console.log(data);
            } else {
              showError('Could not load product.');
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
