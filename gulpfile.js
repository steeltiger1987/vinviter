var elixir = require('laravel-elixir');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

 elixir(function(mix) {

 	mix.copy('node_modules/dropzone/dist/min/dropzone.min.css', 'public/css/dropzone.min.css');
 	mix.copy('node_modules/dropzone/dist/min/dropzone.min.js', 'public/js/dropzone.min.js');
 	mix.copy('node_modules/magnific-popup/dist/jquery.magnific-popup.min.js', 'public/js/jquery.magnific-popup.min.js');
 	mix.copy('node_modules/magnific-popup/dist/magnific-popup.css', 'public/css/magnific-popup.css');
 	mix.copy('node_modules/sortablejs/Sortable.min.js', 'public/js/sortable.min.js');

 	var options = {
 		includePaths: [
 		'node_modules/foundation-sites/scss',
 		'node_modules/motion-ui/src',
 		'node_modules/foundation-datepicker/css',
 		'node_modules/trumbowyg/dist/ui/sass'
 		]
 	};
 	mix.sass('app.scss', null, options);



 	// var emailOptions = {
 	// 	includePaths: [
 	// 	'node_modules/foundation-sites/scss'		
 	// 	]	
 	// };
 	// mix.sass('email.scss', null, emailOptions);



 	var jQuery = '../../../node_modules/jquery/dist/jquery.js';
 	var foundationJsFolder = '../../../node_modules/foundation-sites/js/';
 	var foundationDatePicker = '../../../node_modules/foundation-datepicker/js/';
 	var trumbowyg = '../../../node_modules/trumbowyg/';

 	mix.scripts([
		// Include jQuery and core Foundations JS
		jQuery,
		foundationJsFolder + 'foundation.core.js',

	   // Include Foundation js componenets
	   foundationJsFolder + 'foundation.abide.js',
	   foundationJsFolder + 'foundation.dropdownMenu.js',
	   foundationJsFolder + 'foundation.util.keyboard.js',
	   foundationJsFolder + 'foundation.util.box.js',
	   foundationJsFolder + 'foundation.util.nest.js',
	   foundationJsFolder + 'foundation.util.mediaQuery.js',
	   foundationJsFolder + 'foundation.util.triggers.js',
	   foundationJsFolder + 'foundation.equalizer.js',
	   foundationJsFolder + 'foundation.tooltip.js',
	   foundationJsFolder + 'foundation.tabs.js',
	   foundationJsFolder + 'foundation.util.timerAndImageLoader.js',
	   foundationJsFolder + 'foundation.reveal.js',

	   foundationDatePicker + 'foundation-datepicker.min.js',

	   trumbowyg + 'dist/trumbowyg.min.js',

	   // Custom JS
	   'script.js'
	   ]);

 	});
