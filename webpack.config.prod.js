var path    = require('path')
var webpack = require('webpack')
var ExtractTextPlugin = require('extract-text-webpack-plugin');
//var appConfig =  require('./config/app.js')

module.exports = {
    entry:  [
        //'./app/boot-client.jsx'
        './frontEnd/boot-client.jsx'
    ],
    output: {
        //path:     path.join(__dirname, 'public'),
        path:     './public',
        filename: 'bundle.js'
    },
    module: {
        loaders: [
            //{test: /\.jsx?$/, exclude: /node_modules/, loaders: ['react-hot','babel'] }
            {
                test: /\.css$/, exclude: [/node_modules/, /public/],
                loader: ExtractTextPlugin.extract('style-loader', 'css-loader?modules&importLoaders=1&localIdentName=[name]__[local]___[hash:base64:5]!postcss-loader')
                //loader: 'style!css-loader?modules&importLoaders=1&localIdentName=[name]__[local]___[hash:base64:5]'
            },
            {test: /\.json$/, exclude: [/node_modules/, /public/], loader: 'json-loader'},
            {test: /\.jsx?$/, exclude: [/node_modules/, /public/], loader: 'react-hot' },
            {test: /\.jsx?$/, exclude: [/node_modules/, /public/], loader: 'babel' }
            //{test: /\.css$/,  loader: "style-loader!css-loader" },
            //{test: /\.less$/, loader: "style-loader!css-loader!less-loader" }
        ]
    },
    plugins: [
        new webpack.optimize.UglifyJsPlugin(),
        new ExtractTextPlugin('css/frontEnd.css', { allChunks: true })
        //new webpack.DefinePlugin({
        //    APP_NAME: `'${appConfig.name}'`,
        //    APP_VERSION: `'${appConfig.version}'`,
        //    APP_HOST: `'${appConfig.host}'`,
        //    APP_PORT: appConfig.nodejsPort,
        //    APP_ENV : `'${appConfig.environment}'`,
        //    WEBPACK_IS_DEVELOPMENT: false,
        //    WEBPACK_IS_PRODUCTION:  true,
        //    WEBPACK_IS_TESTING:     false
        //})
    ]
}