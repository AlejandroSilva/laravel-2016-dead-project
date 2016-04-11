var path    = require('path')
var webpack = require('webpack')
var ExtractTextPlugin = require('extract-text-webpack-plugin');

module.exports = {
    entry:  [
        './frontEnd/boot-client.jsx'
    ],
    output: {
        path:     './public',
        filename: 'bundle.js'
    },
    module: {
        loaders: [
            {
                test: /\.css$/, exclude: [/node_modules/, /public/],
                loader: ExtractTextPlugin.extract('style-loader', 'css-loader?modules&importLoaders=1&localIdentName=[name]__[local]___[hash:base64:5]!postcss-loader')
            },
            {test: /\.json$/, exclude: [/node_modules/, /public/], loader: 'json-loader'},
            {test: /\.jsx?$/, exclude: [/node_modules/, /public/], loader: 'react-hot' },
            {test: /\.jsx?$/, exclude: [/node_modules/, /public/], loader: 'babel' }
        ]
    },
    devtool: 'source-map',
    plugins: [
        new webpack.DefinePlugin({
            'process.env': {'NODE_ENV': JSON.stringify('production')}
        }),
        new webpack.optimize.UglifyJsPlugin(),
        new ExtractTextPlugin('css/frontEnd.css', { allChunks: true })
    ]
}