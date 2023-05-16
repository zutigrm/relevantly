const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const IgnoreEmitPlugin = require('ignore-emit-webpack-plugin');

module.exports = {
    entry: './assets/scss/main.scss', // path to your main scss file
    output: {
        filename: 'main.js',
        path: path.resolve(__dirname, 'assets/css'),
    },
    module: {
        rules: [
            {
                test: /\.scss$/,
                use: [
                    MiniCssExtractPlugin.loader,
                    'css-loader',
                    'sass-loader',
                ],
            },
        ],
    },
    plugins: [
        new MiniCssExtractPlugin({ filename: 'main.css' }),
        new IgnoreEmitPlugin(['main.js', 'main.js.map']) // ignore the .js and .js.map files
    ],
};
