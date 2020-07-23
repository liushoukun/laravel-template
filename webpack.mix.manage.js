
const mix = require('laravel-mix');
require('laravel-mix-eslint');
const path = require('path');
const webpack = require('webpack');
const BundleAnalyzerPlugin = require('webpack-bundle-analyzer').BundleAnalyzerPlugin;

const rawArgv = process.argv.slice(2);
const args = rawArgv.join(' ');
const report = rawArgv.includes('--report');
let plugins = [];
if (report) {
  plugins.push(new BundleAnalyzerPlugin({
    openAnalyzer: true,
  }));
}

mix.setPublicPath('public/manage')
mix.setResourceRoot('/manage')
function resolve(dir) {
  return path.join(
    __dirname,
    '/resources/manage',
    dir
  );
}

Mix.listen('configReady', webpackConfig => {
  // Add "svg" to image loader test
  const imageLoaderConfig = webpackConfig.module.rules.find(
    rule =>
      String(rule.test) ===
      String(/(\.(png|jpe?g|gif|webp)$|^((?!font).)*\.svg$)/)
  );
  imageLoaderConfig.exclude = resolve('icons');
});

mix.webpackConfig({
  resolve: {
    extensions: ['.js', '.vue', '.json'],
    alias: {
      vue$: 'vue/dist/vue.esm.js',
      '@': path.join(__dirname, '/resources/manage'),
    },
  },
  module: {
    rules: [
      {
        test: /\.svg$/,
        loader: 'svg-sprite-loader',
        include: [resolve('icons')],
        options: {
          symbolId: 'icon-[name]',
        },
      },
    ],
  },
  plugins: plugins,
}) .babelConfig({
  presets: [
    // https://github.com/vuejs/vue-cli/tree/master/packages/@vue/babel-preset-app
    '@vue/cli-plugin-babel/preset'
  ],
  plugins: ['dynamic-import-node']
});

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix
  .js('resources/manage/main.js', 'public/manage/js')
  .extract([
    'vue',
    'axios',
    'vuex',
    'vue-router',
    'vue-i18n',
    'element-ui',
    'echarts',
    'highlight.js',
    'sortablejs',
    'dropzone',
    'xlsx',
    'tui-editor',
    'codemirror',
  ])
  .options({
    processCssUrls: false,
  })
  .sass('resources/manage/styles/index.scss', 'public/manage/css/app.css', );

if (mix.inProduction()) {
  mix.version();
} else {
  if (process.env.LARAVUE_USE_ESLINT === 'true') {
    mix.eslint();
  }
  // Development settings
  mix
    .sourceMaps()
    .webpackConfig({
      devtool: 'cheap-eval-source-map', // Fastest for development
    });
}
