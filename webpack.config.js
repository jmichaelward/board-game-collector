const path = require('path');
const { CleanWebpackPlugin } = require('clean-webpack-plugin');
const srcRoot = './app/src';

module.exports = {
  entry: {
    settings: `${srcRoot}/settings.js`,
    blocks: `${srcRoot}/blocks/game/index.js`,
  },
  output: {
    path: path.resolve(__dirname, 'app/dist'),
    filename: '[name].js'
  },
  module: {
    rules: [
      {
        test: /\.jsx?$/,
        exclude: /node_modules/,
        loader: 'babel-loader',
        options: {
          presets: [
            '@babel/preset-env',
            '@babel/preset-react',
          ]
        },
        sideEffects: false,
      }
    ],
  },
  plugins: [
    new CleanWebpackPlugin({
      verbose: true,
    }),
  ],
}