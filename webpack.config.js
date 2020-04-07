const Encore = require('@symfony/webpack-encore');

if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')

    .addEntry('main', './assets/js/main.js')
    .addEntry('manufacturers', './assets/js/manufacturers.js')
    .addEntry('ships', './assets/js/ships.js')
    .addEntry('ship_chassis', './assets/js/ships_chassis.js')

    .splitEntryChunks()
    .enableSingleRuntimeChunk()

    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())
    .enableIntegrityHashes(Encore.isProduction())

    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = 3;
    })

    .copyFiles({
        from: './assets/js/vendors',
        to: 'vendors/[path][name].[ext]',
    })
    .copyFiles({
        from: './assets/static/images',
        to: 'static/images/[path][name].[ext]',
    })

    .enableSassLoader()
    .enablePostCssLoader()
    .autoProvidejQuery()
;

module.exports = Encore.getWebpackConfig();
