const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const extractCSS = new MiniCssExtractPlugin({
  filename: 'css/[name].css',
});

const IS_DEV = (process.env.NODE_ENV === 'development');

const config = {
  mode: IS_DEV ? 'development' : 'production',
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
        query: {
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
        query: {
          // This option makes ESLint automatically fix minor issues
          fix: true,
        },
      }]
    }, {
      test: /\.s?css$/,
      use: [
        MiniCssExtractPlugin.loader,
        {
          loader: 'css-loader'
        }, {
          loader: 'postcss-loader',
          query: {
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
    extensions: ['*', '.js', '.scss']
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
  config.devtool = 'eval-source-map';
}

module.exports = config;