const mode = process.env.NODE_ENV;
const isDevMode = mode !== "production";
const config = require('config');
const webpack = require('webpack');
const path = require('path');

const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const outputAbsPath = path.resolve(config.get('publicPath')); //Get absolute path to public

module.exports = {

    mode: isDevMode? 'development' : 'production',

    context: __dirname + '/src',

    entry: {
        "wpe-lib-admin": "./admin/index.js",
        "wpe-lib-frontend": "./frontend/index.js",
        "wpe-lib-common": "./common/index.js"
    },

    externals: {
        jquery: 'jQuery'
    },

    output: {
        path: outputAbsPath,
        filename: "assets/js/[name].bundle.js"
    },

    module: {
        rules: [
            {
                test: /\.scss$/,
                use: [{
                    loader: MiniCssExtractPlugin.loader,
                }, {
                    loader: 'css-loader'
                }, {
                    loader: 'resolve-url-loader'
                }, {
                    loader: 'sass-loader?sourceMap'
                }
                ]
            },
            {
                /* copies images to assets/images, embeds images under 8192 Bytes, links to over 8192*/

                test: /\.(png|jpg|gif|svg|woff|woff2|eot|ttf)$/,
                use: [
                    {
                        loader: 'url-loader',
                        options: {
                            limit: 8192,
                            fallback: 'file-loader',
                            name: '[path][name].[ext]',
                            outputPath: '',
                            publicPath: "../"
                        }
                    }
                ]

            },

            {
                test: /\.js$/,
                exclude: /node_modules|bower_components|vendor/,
                loader: 'babel-loader'
            }],

    },
// optimization
    optimization: {
        splitChunks: {
            cacheGroups: {
                default: false,
                vendors: false,

                // vendor chunk
                vendor: {
                    // name of the chunk
                    name: 'wpe-lib-vendor',

                    // async + async chunks
                    chunks: 'all',

                    // import file path containing node_modules
                    test: /node_modules/,

                    // priority
                    priority: 20
                },

                // common chunk
                common: {
                    name: 'wpe-lib-common',
                    minChunks: 2,
                    chunks: 'all',
                    priority: 10,
                    reuseExistingChunk: true,
                    enforce: true
                }
            }
        }
    },

    plugins:
        [
            new webpack.DefinePlugin({
                'process.env': {
                    NODE_ENV: JSON.stringify(mode)
                }
            }),

            /*
            new webpack.ProvidePlugin(
                {
                    $: "jquery"
                }
            ),
             */

            new MiniCssExtractPlugin({
                // Options similar to the same options in webpackOptions.output
                // both options are optional
                filename: "assets/css/[name].style.css",
                chunkFilename: "assets/css/[name].bundle.css"
            })
        ]
};

