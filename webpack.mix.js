const mix = require('laravel-mix')

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

mix.setPublicPath('public')
	.setResourceRoot('../') // turns assets paths in css relative to css file
	.sass('resources/sass/frontend/app.scss', 'css/frontend.css')
	.sass('resources/sass/backend/app.scss', 'css/backend.css')
	.js(['resources/js/backend/before.js',
		'resources/js/backend/after.js'
	], 'js/backend.js')
	.js(['resources/js/backend/app.js'
	], 'js/app.js').browserSync('http://127.0.0.1:8000')
	.extract([
		/* Extract packages from node_modules, only those used by front and
        backend, to VendorLinks.js */
		'jquery',
		'bootstrap',
		'popper.js'
	])
	.sourceMaps()
if (mix.inProduction()) {
	mix.version()
		.options({
			// optimize js minification process
			terser: {
				cache: true,
				parallel: 4,
				sourceMap: false
			},
			CssPurifierPlugin: {}
		})
} else {
	mix.webpackConfig({
		devtool: 'inline-source-map',
        devServer: {
            port: 4000 // Change the port to the value you want
        },
	})
}
