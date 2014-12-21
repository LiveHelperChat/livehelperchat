var settings = {}

// If you will be using only web application set this to true, will save some resource
settings.web = {};
settings.debug = {};

/**
 * Set path where socket.io is located, it can be also just socket.io
 * */
settings.socketiopath = '/usr/local/lib/node_modules/socket.io';

/**
 * Set your settings
 * */
settings.web.host = "localhost";
settings.web.port = 31130;

/**
 * Enable debug output
 * */
settings.debug.output = true;

module.exports = settings;