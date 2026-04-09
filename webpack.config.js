const path = require('path');

module.exports = {
    entry: {
        'frontend-bundle': './scripts/src/frontend.js',
        'builder-bundle': './scripts/src/loader.js',
    },
    output: {
        path: path.resolve(__dirname, 'scripts'),
        filename: '[name].min.js',
    },
    module: {
        rules: [
            {
                test: /\.jsx?$/,
                exclude: /node_modules/,
                use: {
                    loader: 'babel-loader',
                    options: {
                        presets: ['@babel/preset-env', '@babel/preset-react'],
                    },
                },
            },
        ],
    },
    resolve: {
        extensions: ['.js', '.jsx'],
    },
    externals: {
        jquery: 'jQuery',
        react: 'React',
        'react-dom': 'ReactDOM',
    },
};
