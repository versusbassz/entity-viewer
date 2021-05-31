const path = require('path');
const webpack = require('webpack');

// eslint-disable-next-line no-unused-vars
module.exports = (env, argv) => {
  const isModeProduction = argv.mode === 'production';

  const mode = isModeProduction ? 'production' : 'development';
  console.log(`[META-VIEWER] Webpack mode = ${mode}`);

  // Devtool
  const sourceMaps = isModeProduction ? 'source-map' : 'inline-source-map';

  // Plugins
  const plugins = [];

  plugins.push(new webpack.ProvidePlugin({
    $: 'jquery',
    jQuery: 'jquery',
  }));

  return {
    mode,
    entry: './assets/js/main.js',
    output: {
      filename: 'meta-viewer.build.js',
      path: path.resolve(__dirname, 'assets/build'),
      clean: true,
    },
    devtool: sourceMaps,
    module: {
      rules: [
        {
          test: /\.js$/,
          exclude: '/node_modules/',
          include: [
            path.resolve(__dirname, 'assets/js'),
          ],
          use: {
            loader: 'babel-loader',
          },
        },
        {
          test: /\.(scss|css)$/,
          use: ['style-loader', 'css-loader', 'sass-loader'],
        },
      ],
    },
    externals: {
      jquery: 'jQuery',
    },
    plugins,
  };
};
