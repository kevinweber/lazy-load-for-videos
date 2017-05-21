const ExtractTextPlugin = require('extract-text-webpack-plugin');

module.exports = {
  entry: {
    admin: './modules/admin/index.js',
    'lazyload-all': './modules/lazyload-all/index.js',
    'lazyload-vimeo': './modules/lazyload-vimeo/index.js',
    'lazyload-youtube': './modules/lazyload-youtube/index.js',
  },
  module: {
    rules: [{
      test: /\.(png|svg|jpg|gif)$/,
      use: [{
        loader: 'file-loader',
        options: {
          name: 'media/[name].[ext]',
          publicPath: '../',
        },
      }],
    }, {
      test: /\.js$/,
      exclude: /node_modules/,
      use: [{
        loader: 'babel-loader'
      }, {
        loader: 'eslint-loader',
        options: {
          // This option makes ESLint automatically fix minor issues
          fix: true,
        },
      }]
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
    path: __dirname + '/assets/',
    filename: 'js/[name].js',
  },
  plugins: [
    new ExtractTextPlugin('css/[name].css'),
  ],
  devtool: "#cheap-source-map",
};
