
    'use strict';
    var angular = require('angular');


    angular
        .module('app.products')
        .controller('ProductsController', ProductsController);

    ProductsController.$inject = ['$scope',
                                  '$state',
                                  'SweetAlert',
                                  'TypesService',
                                  'CategoriesService',
                                  'ProductsService'
                                ];

    /* @ngInject */
    function ProductsController($scope,
                                $state,
                                SweetAlert,
                                TypesService,
                                CategoriesService,
                                ProductsService
                              ) {
        var vm = this;

        vm.products = [];
        vm.addProduct = addProduct;
        vm.saveNewProduct = saveNewProduct;
        vm.deleteProduct = deleteProduct;

        vm.availableTypes = [];
        vm.availableCategories = [];

        vm.productImageUploaded = productImageUploaded;
        vm.productFullImageUploaded = productFullImageUploaded;

        vm.newproduct = {
          name: '',
          description: '',
          category_id: null,
          type_id: null,
          frame_start: 1,
          frame_end: 1,
          image: '',
          fullimage: '',
          location: ''
        };

        vm.storageLocation = {
          'S3': 'https://s3-eu-west-1.amazonaws.com/wemockupstorage/'
        }

        /////////////////////////////////////////////////
        activate();

        function activate() {
            getAvailableTypes();
            getAvailableCategories();
            getProducts();
        }

        /////////////////////////////////////////////////


        function deleteProduct(product, index) {
          ProductsService.deleteProduct(product)
          .then(function(data){
            if (data.status == 200) {
              vm.products.splice(index,1);
            } else {
              showError('Could not delete products');
            }
          });
        }


        function getProducts() {
          ProductsService.getAll()
          .then(function(data){
            if (data.status == 200) {
              vm.products = data.data;
            } else {
              showError('Could not load products');
            }
          });
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
          console.log(files);
          vm.newproduct.image = vm.storageLocation.S3 + files[0].key;
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
          vm.newproduct.fullimage = vm.storageLocation.S3 + files[0].key;
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



        function addProduct() {
          vm.newproduct = {};
          openAddModal();
        }


        function openAddModal() {
          $('#addModal').modal('show');
        }

        function closeAddModal() {
          $('#addModal').modal('hide');
        }




        /**
         * Process saving of a new product
         *
         *
         *
         * @return {[type]} [description]
         */
        function saveNewProduct() {


              //add
              ProductsService.addNew(vm.newproduct)
              .then(function(data){
                if (data.status == 200) {
                  //console.log(data.data);
                  vm.products.push(data.data);
                  closeAddModal();
                  //$state.go('products.view',{productid: data.data.id});
                } else {
                  closeAddModal();
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
