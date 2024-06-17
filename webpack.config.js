const Encore = require("@symfony/webpack-encore");

Encore
    // Directory where compiled assets will be stored
    .setOutputPath("public/build/")
    // Public path used by the web server to access the output path
    .setPublicPath("/build")
    // Only needed for CDN's or sub-directory deploy
    //.setManifestKeyPrefix('build/')

    /*
     * ENTRY CONFIG
     *
     * Add 1 entry for each "page" of your app
     * (including one that's included on every page - e.g. "app")
     *
     * Each entry will result in one JavaScript file (e.g. app.js)
     * and one CSS file (e.g. app.css) if your JavaScript imports CSS.
     */
    .addEntry("app", "./assets/app.js")
    .addEntry("chartjs.min", "./assets/js/plugins/chartjs.min.js")
    .addEntry("Chart.extension", "./assets/js/plugins/Chart.extension.js")
    .addEntry(
        "perfect-scrollbar.min",
        "./assets/js/plugins/perfect-scrollbar.min.js"
    )
    .addEntry("carousel", "./assets/js/carousel.js")
    .addEntry("charts", "./assets/js/charts.js")
    .addEntry("dropdown", "./assets/js/dropdown.js")
    // .addEntry("fixed-plugin", "./assets/js/fixed-plugin.js")
    .addEntry("nav-pills", "./assets/js/nav-pills.js")
    .addEntry("navbar-collapse", "./assets/js/navbar-collapse.js")
    .addEntry("navbar-sticky", "./assets/js/navbar-sticky.js")
    .addEntry("perfect-scrollbar", "./assets/js/perfect-scrollbar.js")
    .addEntry("sidenav-burger", "./assets/js/sidenav-burger.js")
    .addEntry("tooltips", "./assets/js/tooltips.js")

    // When enabled, Webpack "splits" your files into smaller pieces for greater optimization.
    .splitEntryChunks()

    // Will require an extra script tag for runtime.js
    // but, you probably want this, unless you're building a single-page app
    .enableSingleRuntimeChunk()

    /*
     * FEATURE CONFIG
     *
     * Enable & configure other features below. For a full
     * list of features, see:
     * https://symfony.com/doc/current/frontend.html#adding-more-features
     */
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    // Enables hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())

    // Enables @babel/preset-env polyfills
    .configureBabel(() => {}, {
        useBuiltIns: "usage",
        corejs: 3,
    })

    // copi images folder
    .copyFiles({
        from: "./assets/images",
        to: "images/[path][name].[ext]",
        pattern: /\.(svg|png|jpg|jpeg|webp|ico)$/,
    })

    // Enables Sass/SCSS support
    //.enableSassLoader()

    // Uncomment if you use TypeScript
    //.enableTypeScriptLoader()

    // Uncomment if you use React
    //.enableReactPreset()

    // PostCSS support
    .enablePostCssLoader();

module.exports = Encore.getWebpackConfig();
