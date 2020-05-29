const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const extractCSS = new MiniCssExtractPlugin({
  filename: 'css/[name].css',
});

const IS_DEV = (process.env.NODE_ENV === 'development');

const config = {
  mode: IS_DEV ? 'development' : 'production',
  entry: {
    admin: './modules/admin/webpackEntry.js',
    'lazyload-all': './modules/lazyload-all/webpackEntry.js',
    'lazyload-vimeo': './modules/lazyload-vimeo/webpackEntry.js',
    'lazyload-youtube': './modules/lazyload-youtube/webpackEntry.js',
  },
  module: {
    rules: [{
      test: /\.(png|svg|jpg|gif)$/,
      exclude: /node_modules/,
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
          cache: true,
          // This option makes ESLint automatically fix minor issues
          fix: true,
        },
      }]
    }, {
      test: /\.tsx?$/,
      exclude: /node_modules/,
      use: [{
        loader: 'babel-loader'
      }, {
        loader: 'ts-loader',
        options: {
          transpileOnly: true,
          experimentalWatchApi: true,
          happyPackMode: IS_DEV,
        },
      }]
    }, 
      {
      test: /\.s?css$/,
      exclude: /node_modules/,
      use: [
        MiniCssExtractPlugin.loader,
        {
          loader: 'css-loader'
        }, {
          loader: 'postcss-loader',
          options: {
            plugins: (loader) => {
              const plugins = [];
              plugins.push(require('autoprefixer'));
              return plugins;
            },
          },
        }, {
          loader: 'sass-loader'
        }
      ],
    }]
  },
  resolve: {
    extensions: ['*', '.ts', '.tsx', '.js', '.scss']
  },
  output: {
    path: __dirname + '/assets/',
    filename: 'js/[name].js',
  },
  plugins: [
    extractCSS,
  ],
};

if (IS_DEV) {
  config.devtool = 'eval-cheap-module-source-map';
}

module.exports = config;