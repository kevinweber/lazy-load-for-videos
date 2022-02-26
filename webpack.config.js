const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const TerserPlugin = require('terser-webpack-plugin');
const ESLintPlugin = require('eslint-webpack-plugin');

const IS_DEV = process.env.NODE_ENV === 'development';

const extractCSS = new MiniCssExtractPlugin({
  filename: 'css/[name].css',
});

const eslint = new ESLintPlugin({
  cache: IS_DEV,
  // This option makes ESLint automatically fix minor issues
  fix: !IS_DEV,
});

const config = {
  // Target is necessary in Webpack 5 to support IE11
  target: ['web', 'es5'],
  mode: IS_DEV ? 'development' : 'production',
  entry: {
    editor: './src/frontend/editor/webpackEntry.ts',
    admin: './src/frontend/admin/webpackEntry.js',
    'lazyload-vimeo': './src/frontend/lazyload-vimeo/webpackEntry.js',
    'lazyload-youtube': './src/frontend/lazyload-youtube/webpackEntry.js',
  },
  module: {
    rules: [
      {
        test: /\.(png|svg|jpg|gif)$/,
        exclude: /node_modules/,
        use: [
          {
            loader: 'file-loader',
            options: {
              name: 'media/[name].[ext]',
              publicPath: '../',
            },
          },
        ],
      },
      {
        test: /\.jsx?$/,
        exclude: /node_modules/,
        use: [
          {
            loader: 'babel-loader',
          },
        ],
      },
      {
        test: /\.tsx?$/,
        exclude: /node_modules/,
        use: [
          {
            loader: 'babel-loader',
          },
          {
            loader: 'ts-loader',
            options: {
              transpileOnly: true,
              experimentalWatchApi: true,
              happyPackMode: IS_DEV,
            },
          },
        ],
      },
      {
        test: /\.s?css$/,
        exclude: /node_modules/,
        use: [
          MiniCssExtractPlugin.loader,
          {
            loader: 'css-loader',
          },
          {
            loader: 'postcss-loader',
            options: {
              postcssOptions: {
                plugins: [
                  'autoprefixer',
                ],
              },
            },
          },
          {
            loader: 'sass-loader',
          },
        ],
      },
    ],
  },
  resolve: {
    extensions: ['*', '.ts', '.tsx', '.js', '.scss'],
  },
  output: {
    path: `${__dirname}/public/`,
    filename: 'js/[name].js',
  },
  plugins: [
    extractCSS,
    eslint,
  ],
  externals: {
    // Manually import used WP packages because if we would use
    // "@wordpress/dependency-extraction-webpack-plugin" instead,
    // our use of "@wordpress/block-library" would throw an error
    // because we're directly accessing internals that aren't
    // exposed via the wp.blockLibrary API
    '@wordpress/data': 'wp.data',
    '@wordpress/i18n': 'wp.i18n',
    '@wordpress/element': 'wp.element',
    '@wordpress/compose': 'wp.compose',
    '@wordpress/components': 'wp.components',
    '@wordpress/hooks': 'wp.hooks',
    '@wordpress/blocks': 'wp.blocks',
    '@wordpress/block-editor': 'wp.blockEditor',
    '@wordpress/block-library': 'wp.blockLibrary',
    lodash: 'lodash',
  },
  optimization: {
    minimizer: [
      new TerserPlugin({
        extractComments: false,
      }),
    ],
    splitChunks: {
      cacheGroups: {
        // Move shared code into a separate bundle
        sharedProduction: {
          name: 'lazyload-shared',
          test: /\/shared(-utils)?\//,
          chunks(chunk) {
            // Exclude chunks
            return chunk.name !== 'admin' && chunk.name !== 'editor';
          },
          priority: -10,
          minSize: 0,
        },
      },
    },
  },
};

if (IS_DEV) {
  config.devtool = 'eval-cheap-module-source-map';
}

module.exports = config;
