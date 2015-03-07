// https://github.com/christianalfoni/webpack-example
var webpack = require('webpack');
var path = require('path');
var bower_dir = path.join(__dirname, 'bower_components');
var node_modules_dir = path.join(__dirname, 'node_modules');

var config = {
   addVendor: function (name, path) {
     this.resolve.alias[name] = path;
     this.module.noParse.push(path);
   },
   context: __dirname,
   entry: {
     app: ['./design/defaulttheme/js/lh/lh.js']
   },
   output: {
     publicPath: '/',
     path: './design/defaulttheme/js/lh/dist/',
     filename: 'bundle.js',
     chunkFilename: '[name]-[chunkhash].js'
   },
   resolve: {
     alias: {},
   	 extensions: ['', '.js', '.json', '.coffee'] 
   },
   module: {
     noParse: [],
     loaders: [{
       test: /\.js$/,
       loader: 'jsx-loader',
       exclude: [bower_dir, node_modules_dir]
     }, {
         test: /\.cssref$/,
         loader: 'style/useable!css!'
     }, 
     {
       test: /\.css$/,
       loader: 'style-loader!css-loader'
     }, {
       test: /\.(woff|png)$/,
       loader: 'url-loader?limit=100000'
     }]
   },
   plugins: [
     new webpack.optimize.CommonsChunkPlugin('app', null, false),     
     new webpack.optimize.UglifyJsPlugin({minimize: true})
   ]
 };

 //config.addVendor('react', path.resolve(bower_dir, 'react/react.min.js'));

 module.exports = config;