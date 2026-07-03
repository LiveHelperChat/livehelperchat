const webpack = require('webpack');
const path = require('path');
const HtmlWebpackPlugin = require('html-webpack-plugin');

const config = {
    entry: './src/index.js',
    experiments: {
        outputModule: true,
    },
    output: {
        library: { type: 'module' },
        path: path.resolve(__dirname, 'dist'),
        filename: 'react.admin.app.js',
        publicPath: "./",
        chunkFilename: "[name].[contenthash].js",
        chunkFormat: 'module',
    },
    devtool: 'source-map',
    module: {
        rules: [
            {
                test: /\.(js|jsx)$/,
                use: 'babel-loader',
                exclude: /node_modules/
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