var settings = {}

// Initialize main variables
settings.web = {};
settings.debug = {};

/**
 * Set path where socket.io is located, it can be also just socket.io
 * */
settings.socketiopath = '/usr/local/lib/node_modules/socket.io';

/**
 * Set your settings
 * */
settings.web.host = "94.23.6.194"; //94.23.6.194
settings.web.port = 31130;

/**
 * Enable debug output
 * */
settings.debug.output = false;

module.exports = settings;