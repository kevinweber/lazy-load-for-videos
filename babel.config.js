module.exports = (api) => ({
  presets: [
    ['@babel/preset-env', {
      browserslistEnv: api.env(),
    }],
    '@babel/preset-react',
  ],
  plugins: ['@babel/plugin-proposal-class-properties'],
});
