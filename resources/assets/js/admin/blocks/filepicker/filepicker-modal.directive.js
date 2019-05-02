(function() {
    'use strict';

    var filepicker = require('filepicker-js');

    var angular = require('angular');
    angular
        .module('blocks.filepicker')
        .directive('filepickermodal', filepickermodal);

    /* @ngInject */
    function filepickermodal() {
        var directive = {
            restrict: 'E',
            templateUrl: 'admin/blocks/filepicker/partials/button.html',
            scope: {
              'debug': '@', // false by default.  If true picker just returns dummy data straight away
              callback: '&', // the callback function that filepicker calls on finished
              'pickerclass': '@', // style class for btn
              'buttontext': '@', // button text for the file picker
              'iconclass': '@', // icon class for the button
              'multiple': '@', // can pick multiple files true | false
              'maxfiles': '@', // Maximum files allowed to be picked at 1 time.
              'folders': '@', // can users drop whole folders true | false
              'mimetype': '@', // eg image/* for all images, or  application/msword Word docs
              'mimetypes': '@', // comma list of types eg image/*,text/*
              'extension': '@', // eg .pdf
              'extensions': '@', // comma list of file ext eg .png,.pdf,.js
              'maxsize': '@', //bytes maximum - default unlimited
              'service': '@', // eg COMPUTER
              'services': '@', // Comma list COMPUTER,FACEBOOK,BOX
              'backgroundupload': '@', // allow background upload.  default false;
              'location': '@', //'S3', 'azure', 'dropbox', 'rackspace' and 'gcs' - Defaults to azure
              'path': '@', // path to store the file
              'access': '@', // public or private by default
              //IMAGE MANIPULATIONS
              'imagemanipulations': '@', // flag to trigger image manipulations default: false
              'imagequality': '@', // image quality default 100, only works for jpeg images
              'imagedim': '@', // width,height - images will be upscaled or downscaled (you can use 'null' for width or height)
              'imagedimmax': '@', // width,height - Images bigger than the specified dimensions will be resized to the max size
              'imagedimmin': '@', // width,height - Images smaller than the specified dimensions will be upscaled to the minimum
              //IMAGE CROPPING
              'imagecropui': '@', // set to true to allow image cropping/filters tool
              'imagecropforce': '@', // force images to be cropped (multiple images can be used)
              'imagecropratio': '@', // Specify the crop area height to width ratio. This can be a float, an integer or a ratio like 4/3 or 16/9.
              'imagecropmax': '@', // width,height eg. 400,800 Specify the maximum dimensions of the crop area.
              'imagecropmin': '@' // width,height eg. 30,30 Specify the minimum dimensions of the crop area.

            },
            link: linkFunc
        };

        return directive;

        function linkFunc(scope, el, attr, ctrl) {

          scope.pickFiles = function () {


                var apikey = 'ALTm0uWhxTzKUZWuy6VCrz';

                var defaultServices = [
                  'COMPUTER',
                  'WEBCAM',
                  'VIDEO',
                  'AUDIO',
                  'DROPBOX',
                  'EVERNOTE',
                  'BOX',
                  'GOOGLE_DRIVE',
                  'SKYDRIVE'
                ];


                /////////////////////////////////////
                //Picker options
                var pickeroptions = {
                  debug: attr.debug ? attr.debug : false,
                  maxFiles: attr.maxfiles ? attr.maxfiles : '',
                  folders: attr.folders ? attr.folders : true,
                  maxSize: attr.maxsize ? attr.maxsize : '',
                  backgroundUpload: attr.backgroundupload ? attr.backgroundupload : false
                };

                if (attr.multiple) {
                  pickeroptions.multiple = false;
                } else {
                  pickeroptions.multiple = true;
                }

                if (attr.mimetypes) {
                  pickeroptions.mimetypes = attr.mimetypes.split(',');
                }
                if (attr.mimetype) {
                  pickeroptions.mimetype = attr.mimetype ? attr.mimetype : '';
                }

                if (attr.extensions) {
                  pickeroptions.extensions = attr.extensions.split(',');
                }
                if (attr.extension) {
                  pickeroptions.extension = attr.extension ? attr.extension : '';
                }

                if (attr.services) {
                  pickeroptions.services = attr.services.split(',');
                } else {
                  pickeroptions.services = defaultServices;
                }



                ///////////////////////////////////////
                //image manipulations
                if (attr.imagemanipulations) {

                  //set image quality
                  if (attr.imagequality) {
                    pickeroptions.imageQuality = attr.imagequality;
                  }

                  //Image bounding dimensions
                  if (attr.imagedim) {
                    pickeroptions.imageDim = attr.imagedim.split(',');
                  }

                  //image maximum dims
                  if (attr.imagedimmax) {
                    pickeroptions.imageMax = attr.imagedimmax.split(',');
                  }

                  if (attr.imagedimmin) {
                    pickeroptions.imageMin = attr.imagedimmin.split(',');
                  }

                  //Crop UI - turn on cropping and filters
                  if (attr.imagecropui) {

                      pickeroptions.conversions = ['crop', 'rotate', 'filter'];
                      pickeroptions.services.push('CONVERT');

                      //force cropping
                      if (attr.imagecropforce) {
                        pickeroptions.cropForce = true;
                      }

                      //fix the crop h/w ratio
                      if (attr.imagecropratio) {
                        pickeroptions.cropRatio = attr.imagecropratio;
                      }


                      //fix max and min crop areas allowed
                      if (attr.imagecropmax) {
                        pickeroptions.cropMax = attr.imagecropmax.split(',');
                      }

                      if (attr.imagecropmin) {
                        pickeroptions.cropMin = attr.imagecropmin.split(',');
                      }

                      


                  }
                  //crop ui end

                }


                ///////////////////////////////////////
                //Storage options
                var storageoptions = {
                  location: attr.location ? attr.location : 'S3',
                  path: attr.path ? attr.path : '',
                  access: attr.access ? attr.access : 'private'
                };



                ///////////////////////////////////////
                //filepicker execute
                filepicker.setKey(apikey);
                filepicker.pickAndStore(pickeroptions, storageoptions, function (fpfiles) {
                    scope.$apply(function () {
                        scope.callback({file: fpfiles});
                    });
                });
          };


        }
    }
})();
