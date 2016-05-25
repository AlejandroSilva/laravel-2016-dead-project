var path    = require('path')
var webpack = require('webpack')
var ExtractTextPlugin = require('extract-text-webpack-plugin')

module.exports = {
    entry:  [
        './frontEnd/boot-client.jsx'
    ],
    output: {
        path:     './public/app',
        filename: 'bundle.js'
    },
    module: {
        loaders: [
            // CSS globales (de librerias)
            // {
            //     test: /\.css$/, include: [/node_modules/], exclude: [/public/],
            //     loader: 'style-loader!css-loader'},
            // {
            //     test: /\.woff(2)?(\?v=[0-9].[0-9].[0-9])?$/, include: [/node_modules/], exclude: [/public/],
            //     loader: "url-loader?mimetype=application/font-woff"
            // },
            // {
            //     test: /\.(ttf|eot|svg)(\?v=[0-9].[0-9].[0-9])?$/, include: [/node_modules/], exclude: [/public/],
            //     loader: "file-loader?name=[name].[ext]"
            // },
            // {
            //     test: /\.gif$/, include: [/node_modules/], exclude: [/public/],
            //     loader: "url-loader?mimetype=image/png"
            // },

            // CSS Minificado
            {
                test: /\.css$/, exclude: [/node_modules/, /public/],
                loader: ExtractTextPlugin.extract('style-loader', 'css-loader?modules&importLoaders=1&localIdentName=[name]__[local]___[hash:base64:5]!postcss-loader')
            },
            {test: /\.json$/, exclude: [/node_modules/, /public/], loader: 'json-loader'},
            {test: /\.jsx?$/, exclude: [/node_modules/, /public/], loader: 'react-hot' },
            {test: /\.jsx?$/, exclude: [/node_modules/, /public/], loader: 'babel' }
        ]
    },
    devtool: 'eval',
    plugins: [
        //new webpack.optimize.UglifyJsPlugin(),
        new ExtractTextPlugin('frontEnd.css', { allChunks: true })
    ]
}