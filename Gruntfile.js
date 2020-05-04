module.exports = function ( grunt ) {
	grunt.loadNpmTasks( 'grunt-banana-checker' );
	grunt.loadNpmTasks( 'grunt-stylelint' );

	grunt.initConfig( {
		banana: {
			all: 'i18n/'
		},
		stylelint: {
			all: [
				'**/*.css',
				'!public/build/**',
				'!node_modules/**',
				'!vendor/**'
			]
		}
	} );

	grunt.registerTask( 'test', [ 'banana', 'stylelint' ] );
	grunt.registerTask( 'default', 'test' );
};
