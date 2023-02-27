const path = require( 'path' );
const baseConfig = require( '@wordpress/scripts/config/webpack.config.js' );

const common = {
	resolve: {
		...baseConfig.resolve,
		alias: {
			...baseConfig.resolve.alias,
			Root: __dirname,
		},
	},
};

module.exports = {
	...baseConfig,
	...common,
	output: {
		...baseConfig.output,
		path: path.join( __dirname, 'build/js' ),
		filename: '[name].bundle.js',
	},
	entry: {
		'footer-script': './tests/js/footer-script/footer-script.js',
		'header-script': './tests/js/header-script/header-script.js',
		'main': './tests/js/main/main.js',
		'core-paragraph': './tests/js/core-paragraph/core-paragraph.js'
	},
};
