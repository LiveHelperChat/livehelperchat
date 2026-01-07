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
        test: /\.(js|jsx|mjs|ts|tsx)$/,
        use: {
          loader: 'babel-loader',
          options: {
            configFile: path.resolve(__dirname, 'babel.config.js')
          }
        },
        exclude: (modulePath) => {
          // Always transform these packages (handle both Windows \ and Unix / paths)
          if (/node_modules[\\/](react-redux|use-sync-external-store)/.test(modulePath)) {
            return false;
          }
          // Exclude other node_modules
          return /node_modules/.test(modulePath);
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