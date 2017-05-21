const ExtractTextPlugin = require('extract-text-webpack-plugin');

module.exports = {
  entry: {
    admin: './js/admin.js',
    'lazyload-all': './js/lazyload-all.js',
    'lazyload-vimeo': './js/lazyload-vimeo.js',
    'lazyload-youtube': './js/lazyload-youtube.js',
  },
  module: {
    rules: [{
      test: /\.js$/,
      exclude: /node_modules/,
      loader: 'babel-loader'
    }, {
      test: /\.js$/,
      exclude: /node_modules/,
      loader: 'eslint-loader',
      options: {
        // This option makes ESLint automatically fix minor issues
        fix: true,
      },
    }, {
      test: /\.s?css$/,
      use: ExtractTextPlugin.extract({
        use: [{
          loader: 'css-loader'
        }, {
          loader: 'sass-loader'
        }]
      })
    }]
  },
  resolve: {
    extensions: ['*', '.js', '.scss']
  },
  output: {
    path: __dirname + '/assets/js/',
    filename: '[name].js',
  },
  plugins: [
    new ExtractTextPlugin('[name].css'),
  ],
  devtool: "#cheap-source-map",
};
