"use strict";

var angular = require('angular');

var core = angular.module('app.core');

/**
 * toastr config
 *
 * @param  {[type]} toastrConfig [description]
 * @return {[type]}              [description]
 */
core.config(toastrConfig);

toastrConfig.$inject = ['toastr'];

function toastrConfig(toastr) {
    toastr.options.timeOut = 4000;
    toastr.options.positionClass = 'toast-bottom-right';
}

var config = {
    appErrorPrefix: '[MyProject Error] ',
    appTitle: 'MyProject'
};

core.value('config', config);


/**
 * Configure Logger and Exception Handler
 *
 *
 * @param  {[type]} configure [description]
 * @return {[type]}           [description]
 */
core.config(configure);

configure.$inject = ['$logProvider', 'routerHelperProvider', 'exceptionHandlerProvider'];

function configure($logProvider, routerHelperProvider, exceptionHandlerProvider) {
    if ($logProvider.debugEnabled) {
        $logProvider.debugEnabled(true);
    }
    exceptionHandlerProvider.configure(config.appErrorPrefix);
    routerHelperProvider.configure({docTitle: config.appTitle + ': '});

}


/**
 * Configure loading spinner
 *
 * @param  {[type]} loadingSpinnerConfig [description]
 * @return {[type]}                      [description]
 */
core.config(loadingSpinnerConfig);

loadingSpinnerConfig.$inject = ['cfpLoadingBarProvider'];

function loadingSpinnerConfig(cfpLoadingBarProvider) {
    cfpLoadingBarProvider.includeSpinner = true;
    cfpLoadingBarProvider.includeBar = true;
    cfpLoadingBarProvider.latencyThreshold = 0; // set to 50 on production
}
