
    'use strict';
var angular = require('angular');



    angular
        .module('app.products')
        .service('InputTypesService', InputTypesService);

    InputTypesService.$inject = ['$http'];

    /* @ngInject */
    function InputTypesService($http) {

        /**
         * Data templates for input types
         *
         *
         *
         *
         *
         * @type {Object}
         */
        var inputdata = {
          'imageupload': {
            'imagecropratio': '4/3' // Specify the crop area height to width ratio. This can be a float, an integer or a ratio like 4/3 or 16/9.
          },
          'videoupload': {},
          'text': {},
          'dropdown': {
            'dropdowntitle':'Please select option.',
            'options':[
              {'name': 'option1', 'value': 'value1'},
              {'name': 'option2', 'value': 'value2'}
            ]
          }
        };


        var api = {
          getdatatemplate: getdatatemplate
        }

        return api;

        /////////////////////////////////////////


        function getdatatemplate(input_type) {
          switch (input_type) {
            case 'imageupload':
              return angular.toJson(inputdata.imageupload, true);
              break;
              case 'videoupload':
                return angular.toJson(inputdata.videoupload,true);
                break;
                case 'text':
                  return angular.toJson(inputdata.text,true);
                  break;
                  case 'dropdown':
                    return angular.toJson(inputdata.dropdown,true);
                    break;
            default:
              return '';

          }
        }


    }
