( function () {

	var Encore = require( '@symfony/webpack-encore' );

	Encore
		.setOutputPath( 'public/build/' )
		.setPublicPath( 'build' )

		.addEntry( 'app', [
			'./node_modules/normalize.css/normalize.css',
			'./node_modules/wikimedia-ui-base/wikimedia-ui-base.css',
			'./assets/css/app.css',
			'./assets/js/app.js'
		] )

		.splitEntryChunks()
		.enableSingleRuntimeChunk()
		.cleanupOutputBeforeBuild()
		.enableSourceMaps( !Encore.isProduction() )
		.enableVersioning( Encore.isProduction() )

		// enables @babel/preset-env polyfills
		.configureBabelPresetEnv( function ( config ) {
			config.useBuiltIns = 'usage';
			config.corejs = 3;
		} );

	module.exports = Encore.getWebpackConfig();

}() );
