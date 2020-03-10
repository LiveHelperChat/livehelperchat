const webpack = require('webpack');
const path = require('path');
const HtmlWebpackPlugin = require('html-webpack-plugin');

const config = {
  entry: './src/react.app.js',
  output: {
    library: 'LHCReactAPP',
    libraryTarget: 'umd',
    libraryExport: 'default',
    path: path.resolve(__dirname, 'dist'),
    filename: 'react.app.js',
    publicPath: "./",
    chunkFilename: "[name].[contenthash].js"
  },
  module: {
    rules: [
      {
        test: /\.(js|jsx)$/,
        use: 'babel-loader',
        exclude: /node_modules/
      },
      {
        test: /bootstrap\.native/,
        use: {
            loader: 'bootstrap.native-loader',
            options: {
                only: ['modal', 'dropdown', 'tab']
            }
        }
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