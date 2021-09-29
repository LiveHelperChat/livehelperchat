const webpack = require('webpack');
const path = require('path');
const HtmlWebpackPlugin = require('html-webpack-plugin');

const config = {
    entry: './src/react.app.ie.js',
    output: {
        environment: {
            // The environment supports arrow functions ('() => { ... }').
            // The environment supports arrow functions ('() => { ... }').
            arrowFunction: false,
            // The environment supports BigInt as literal (123n).
            bigIntLiteral: false,
            // The environment supports const and let for variable declarations.
            const: false,
            // The environment supports destructuring ('{ a, b } = obj').
            destructuring: false,
            // The environment supports an async import() function to import EcmaScript modules.
            dynamicImport: false,
            // The environment supports 'for of' iteration ('for (const x of array) { ... }').
            forOf: false,
            // The environment supports ECMAScript Module syntax to import ECMAScript modules (import ... from '...').
            module: false,
        },
        library: 'LHCReactAPP',
        libraryTarget: 'umd',
        libraryExport: 'default',
        path: path.resolve(__dirname, 'dist'),
        filename: 'react.app.ie.js',
        publicPath: "./",
        chunkFilename: "[name].[contenthash].ie.js",
        crossOriginLoading: "anonymous"
    },
    optimization: {
        splitChunks: {
            cacheGroups: {
                vendor: {
                    test: /[\\/]node_modules[\\/](react|react-dom)[\\/]/,
                    name: 'vendor',
                    filename: 'vendor.ie.js',
                    chunks: 'all',
                },
            },
        },
    },
    target: 'browserslist',
    devtool: 'source-map',
    module: {
        rules: [
            {
                test: /\.(js|jsx)$/,
                use: {
                    loader: 'babel-loader',
                    options: {
                        presets: [
                            ['@babel/preset-env', {
                                useBuiltIns: 'usage',
                                corejs: 3
                            }]
                        ]
                    }
                },
                exclude: /node_modules/,
            },
            {
                test: /bootstrap\.native/,
                use: {
                    loader: 'bootstrap.native-loader',
                    options: {
                        presets: [
                            ['@babel/preset-env', {
                                useBuiltIns: 'usage',
                                corejs: 3
                            }]
                        ],
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