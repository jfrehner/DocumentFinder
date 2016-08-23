var path = require('path')

module.exports = {
  entry: './app/index.js',
  output: {
    path: __dirname + '/dist/',
    publicPath: '/dist/',
    filename: 'bundle.js'
  },
  module: {
    loaders: [{
      test: /.js$/,
      loader: 'babel-loader',
      exclude: /node_modules/,
      query: {
        presets: ['es2015', 'stage-0', 'react']
      }
    }, {
      test: /\.json$/,
      loader: 'json-loader'
    }]
  },
  target: 'electron-renderer'
}
