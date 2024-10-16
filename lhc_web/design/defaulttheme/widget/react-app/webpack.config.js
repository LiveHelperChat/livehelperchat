const webpack = require('webpack');
const path = require('path');


const config = {
  entry: './src/react.app.js',
  output: {
    library: 'LHCReactAPP',
    libraryTarget: 'umd',
    libraryExport: 'default',
    path: path.resolve(__dirname, 'dist'),
    filename: 'react.app.js',
    publicPath: "./",
    chunkFilename: "[name].[contenthash].js",
    crossOriginLoading: "anonymous"
  },
  optimization: {
    splitChunks: {
        cacheGroups: {
                vendor: {
                   test: /[\\/]node_modules[\\/](react|react-dom|i18next-http-backend|immutable|axios|html-react-parser|react-i18next|i18next|redux-thunk|redux-promise-middleware|react-redux|bootstrap\.native)[\\/]/,
                   name: 'vendor',
                   filename: 'vendor.js',
                   chunks: 'all',
               },
           },
       },
  },
  devtool: 'source-map',
  module: {
    rules: [
      {
        test: /\.(js|jsx)$/,
        use: 'babel-loader',
        exclude: /node_modules/
      },
      {
        test: /\.(js|jsx)$/,
        use: 'babel-loader',
        include: /node_modules\/socketcluster-client/
      }
    ]
  },
  resolve: {
    extensions: [
      '.js',
      '.jsx'
    ]
  },
  plugins: [
  ]
};

module.exports = config;