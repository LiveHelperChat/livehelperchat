
var webpack = require('webpack');
var path = require('path');
var bower_dir = path.join(__dirname, 'bower_components');
var node_modules_dir = path.join(__dirname, 'node_modules');

var config = {
   context: __dirname,
   mode: 'production',
   entry: './design/defaulttheme/js/lh/lh.js',
   output: {
     publicPath: '/',
     path: path.resolve(__dirname, 'design/defaulttheme/js/lh/dist'),
     filename: 'bundle.js',
     chunkFilename: '[name]-[chunkhash].js'
   }
 };

 module.exports = config;