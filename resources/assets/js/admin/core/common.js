"use strict";

var settings = require('./config.json');

var config = function() {
     
  var env = process.env.NODE_ENV || 'development';  
  return settings[env];
};

module.exports = {
  config: config
};