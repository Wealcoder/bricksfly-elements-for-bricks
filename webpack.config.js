const defaultConfig = require("@wordpress/scripts/config/webpack.config");
const path = require("path");
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const CssMinimizerPlugin = require("css-minimizer-webpack-plugin");
const RemoveEmptyScriptsPlugin = require("webpack-remove-empty-scripts");
const TerserPlugin = require("terser-webpack-plugin");
const CopyPlugin = require("copy-webpack-plugin");

const isProd =
  process.env.NODE_ENV === "production" ||
  process.argv.includes("--mode=production") ||
  process.argv.includes("-p");

const defaultRulesWithoutBabel = defaultConfig.module.rules.filter(
  (rule) => !rule.use || !JSON.stringify(rule.use).includes("babel-loader"),
);

/* -------------------------------------------------------------------------
   Shared element entries
------------------------------------------------------------------------- */

const ELEMENT_ENTRIES = [
  // JS + SCSS pairs
  "animated-heading",
  "brand-slider",
  "button-pro",
  "counter",
  "draggable-items",
  "icon-box",
  "image-accordion",
  "post-social-share",
  "progressbar",
  "testimonial",
  "testimonial-2",
  "testimonial-3",
  "toggle-switch",
  "video-box-slider",
  "video-box",
  "video-mask",
  "video-popup",
  "video-story",
  // CSS-only (JS is a 1-line stub that imports the SCSS)
  "floating-elements",
  "meta-info",
  "posts-slider",
  "social-icons",
  "team",
  "timeline",
];

const elementEntries = ELEMENT_ENTRIES.reduce((acc, name) => {
  acc[`elements/${name}`] = `./src/js/elements/${name}.js`;
  return acc;
}, {});

/* -------------------------------------------------------------------------
   Config 1 — Admin React entries (dashboard, page-import, cpt-builder).
   Uses type: "javascript/esm" + modules: false so webpack correctly handles
   import/export in the React source.
------------------------------------------------------------------------- */

const adminConfig = {
  ...defaultConfig,
  name: "admin",
  mode: isProd ? "production" : "development",
  devtool: isProd ? false : "source-map",
  externals: {
    react: "React",
    "react-dom": "ReactDOM",
  },
  entry: {
    "admin/dashboard": "./src/admin/dashboard/main.js",
    "admin/page-import": "./src/admin/page-import/main.js",
    "modules/cpt-builder/main": "./src/modules/cpt-builder/main.js",
  },
  output: {
    path: path.resolve(__dirname, "public/build"),
    filename: "[name].js",
    clean: false,
  },
  module: {
    ...defaultConfig.module,
    rules: [
      {
        test: /\.(js|jsx)$/,
        exclude: /node_modules/,
        // Force ESM parsing so webpack accepts the import/export statements
        // that Babel leaves intact (package.json has type: "commonjs", which
        // would otherwise make webpack parse .js files as scripts).
        type: "javascript/esm",
        resolve: { fullySpecified: false },
        use: {
          loader: require.resolve("babel-loader"),
          options: {
            presets: [
              [require.resolve("@babel/preset-env"), { modules: false }],
              require.resolve("@babel/preset-react"),
            ],
          },
        },
      },
      ...defaultRulesWithoutBabel,
    ],
  },
  // Drop CleanWebpackPlugin — it would wipe public/build/ which is shared
  // with the bundles config output.
  plugins: defaultConfig.plugins.filter(
    (p) => p.constructor.name !== "CleanWebpackPlugin",
  ),
  resolve: {
    extensions: [".js", ".jsx"],
    alias: {
      "@": path.resolve(__dirname, "src/admin/dashboard"),
      S: path.resolve(__dirname, "src/modules/cpt-builder/"),
    },
  },
  // Only apply minification in production.
  optimization: isProd
    ? {
        minimize: true,
        minimizer: [
          new TerserPlugin({
            extractComments: false,
            terserOptions: { format: { comments: false } },
          }),
        ],
      }
    : { minimize: false },
};

/* -------------------------------------------------------------------------
   Config 2 — Bundled assets (elements, extensions, sitewide, admin vanilla).

   IMPORTANT — SCSS is always extracted to .css files via MiniCssExtractPlugin,
   in BOTH production and development. style-loader is intentionally NOT used,
   because PHP enqueues these .css files directly from disk. style-loader only
   injects styles into the DOM at runtime and never writes a file, which would
   cause missing stylesheet errors in development.
------------------------------------------------------------------------- */

const bundlesConfig = {
  name: "bundles",
  mode: isProd ? "production" : "development",
  devtool: isProd ? false : "source-map",
  entry: {
    ...elementEntries,
    "extensions/editor-panel": "./src/js/extensions/editor-panel.js",
    "extensions/starter-animations-client":
      "./src/js/extensions/starter-animations/client.js",
    "extensions/starter-animations-builder":
      "./src/js/extensions/starter-animations/builder.js",
    frontend: "./src/js/frontend.js",
    // Admin vanilla (non-React) JS + SCSS bundles
    "admin/bricks-animation-addons-admin":
      "./src/js/admin/bricks-animation-addons-admin.js",
    "admin/aab-template-library": "./src/js/admin/aab-template-library.js",
  },
  output: {
    path: path.resolve(__dirname, "public/build"),
    filename: "[name].js",
    assetModuleFilename: "images/[name][ext]",
    clean: false,
  },
  module: {
    rules: [
      {
        test: /\.(js|jsx)$/,
        exclude: /node_modules/,
        use: {
          loader: require.resolve("babel-loader"),
          options: {
            presets: [
              [require.resolve("@babel/preset-env"), { modules: "commonjs" }],
            ],
          },
        },
      },
      {
        test: /\.scss$/,
        // Always extract to a physical .css file — PHP enqueues these directly.
        // Do NOT switch to style-loader in development; it never writes to disk.
        use: [MiniCssExtractPlugin.loader, "css-loader", "sass-loader"],
      },
      {
        test: /\.(png|jpe?g|gif|svg|woff2?|ttf|eot)$/,
        type: "asset/resource",
      },
    ],
  },
  plugins: [
    new RemoveEmptyScriptsPlugin(),
    new MiniCssExtractPlugin({ filename: "[name].css" }),
  ],
  optimization: isProd
    ? {
        minimize: true,
        minimizer: [
          new TerserPlugin({
            extractComments: false,
            terserOptions: { format: { comments: false } },
          }),
          new CssMinimizerPlugin(),
        ],
      }
    : { minimize: false },
};

/* -------------------------------------------------------------------------
   Config 3 - Copy source files for WordPress.org review.
   Copies readable source files to public/build/src/ for review.
------------------------------------------------------------------------- */

const sourceCopyConfig = {
  name: "source-copy",
  mode: "development",
  devtool: false,
  optimization: {
    minimize: false,
  },
  entry: {},
  output: {
    path: path.resolve(__dirname, "public/build"),
  },
  plugins: [
    new CopyPlugin({
      patterns: [
        // Copy Elements JS source files
        {
          context: "src/js/elements",
          from: "**/*.js",
          to: "elements/src/js/[name][ext]",
        },
        // Copy Elements SCSS source files as CSS for review
        {
          context: "src/scss/elements",
          from: "**/*.scss",
          to: "elements/src/css/[name].css",
        },
        // Copy Extensions JS source files
        {
          context: "src/js/extensions",
          from: "**/*.js",
          to: "extensions/src/js/[path][name][ext]",
        },
        // Copy Extensions SCSS source files as CSS for review
        {
          context: "src/scss/extensions",
          from: "**/*.scss",
          to: "extensions/src/css/[name].css",
        },
      ],
    }),
  ],
};

module.exports = [adminConfig, bundlesConfig, sourceCopyConfig];
