
    'use strict';
var angular = require('angular');



    angular
        .module('app.products')
        .service('ProductsService', ProductsService);

    ProductsService.$inject = ['$http'];

    /* @ngInject */
    function ProductsService($http) {

        var api = {
          addNew: addNew,
          deleteProduct: deleteProduct,
          loadProduct: loadProduct,
          saveProduct: saveProduct,
          getAll: getAll,
          addSku: addSku,
          editSku: editSku,
          deleteSku: deleteSku,
          addInputOption: addInputOption,
          editInputOption: editInputOption,
          deleteInputOption: deleteInputOption,
          saveInputOrdering: saveInputOrdering
        }

        return api;

        /////////////////////////////////////////

        function saveProduct(product) {
          return $http({
              url : '/products/' + product.id + '/edit',
              method : 'POST',
              headers : {
                'Content-Type' : 'application/json'
              },
              data : {
                id: product.id,
                name: product.name,
                category_id: product.category_id,
                type_id: product.type_id,
                description: product.description,
                frame_start: product.frame_start,
                frame_end: product.frame_end,
                image: product.image,
                fullimage: product.fullimage,
                location: product.location
              }
          });
        }


        function deleteProduct(product) {
          return $http({
              url : '/products/' + product.id + '/destroy',
              method : 'POST',
              headers : {
                'Content-Type' : 'application/json'
              }
          });
        }

        function saveInputOrdering(orderValues, product) {
          return $http({
              url : '/products/' + product.id + '/inputoptions/order',
              method : 'POST',
              headers : {
                'Content-Type' : 'application/json'
              },
              data : {
                orderValues: orderValues
              }
          });
        }





        function deleteInputOption(inputoption) {
          return $http({
              url : '/products/inputoptions/' + inputoption.id + '/destroy',
              method : 'POST',
              headers : {
                'Content-Type' : 'application/json'
              }
          });
        }



        function addInputOption(inputoption, product) {
          return $http({
              url : '/products/' + product.id + '/inputoptions',
              method : 'POST',
              headers : {
                'Content-Type' : 'application/json'
              },
              data : {
                product_id: product.id,
                name: inputoption.name,
                description: inputoption.description,
                image: inputoption.image,
                input_type: inputoption.input_type,
                data: inputoption.data,
                variable_name: inputoption.variable_name,
                priority: inputoption.priority
              }
          });
        }


        function editInputOption(inputoption) {
          return $http({
              url : '/products/inputoptions/' + inputoption.id + '/edit',
              method : 'POST',
              headers : {
                'Content-Type' : 'application/json'
              },
              data : {
                id: inputoption.id,
                product_id: inputoption.product_id,
                name: inputoption.name,
                description: inputoption.description,
                image: inputoption.image,
                input_type: inputoption.input_type,
                data: inputoption.data,
                variable_name: inputoption.variable_name,
                priority: inputoption.priority
              }
          });
        }


        function editSku(sku) {
          return $http({
              url : '/products/sku/editsku',
              method : 'POST',
              headers : {
                'Content-Type' : 'application/json'
              },
              data : {
                id: sku.id,
                name: sku.name,
                description: sku.description,
                frameconfig_id: sku.frameconfig_id
              }
          });
        }


        function deleteSku(sku) {
          return $http({
              url : '/products/sku/' + sku.id + '/destroy',
              method : 'POST',
              headers : {
                'Content-Type' : 'application/json'
              }
          });
        }



        function addSku(sku) {
          return $http({
              url : '/products/sku',
              method : 'POST',
              headers : {
                'Content-Type' : 'application/json'
              },
              data : {
                product_id: sku.product_id,
                name: sku.name,
                description: sku.description,
                frameconfig_id: sku.frameconfig_id
              }
          });
        }



        function addNew(product) {
          return $http({
              url : '/products',
              method : 'POST',
              headers : {
                'Content-Type' : 'application/json'
              },
              data : {
                name: product.name,
                description: product.description,
                category_id: product.category_id,
                type_id: product.type_id,
                frame_start: product.frame_start,
                frame_end: product.frame_end,
                image: product.image,
                fullimage: product.fullimage,
                location: product.location
              }
          });
        }



        function loadProduct(id) {
          return $http({
              url : '/products/' + id,
              method : 'GET',
              headers : {
                'Content-Type' : 'application/json'
              }
          });
        }


        function getAll() {
          return $http({
              url : '/products/',
              method : 'GET',
              headers : {
                'Content-Type' : 'application/json'
              }
          });
        }



    }
