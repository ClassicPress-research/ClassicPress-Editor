<?php
/**
 * Disable error reporting
 *
 * Set this to error_reporting( -1 ) for debugging.
 */
error_reporting(0);

$basepath = dirname(__FILE__);

function get_file($path) {

	if ( function_exists('realpath') )
		$path = realpath($path);

	if ( ! $path || ! @is_file($path) )
		return false;

	return @file_get_contents($path);
}

//$expires_offset = 31536000; // 1 year
$expires_offset = 60; // 60 seconds for testing

header('Content-Type: application/javascript; charset=UTF-8');
header('Vary: Accept-Encoding'); // Handle proxies
header('Expires: ' . gmdate( "D, d M Y H:i:s", time() + $expires_offset ) . ' GMT');
header("Cache-Control: public, max-age=$expires_offset");
/* temporary: always load the new version
if ( isset($_GET['c']) && 1 == $_GET['c'] && isset($_SERVER['HTTP_ACCEPT_ENCODING'])
	&& false !== stripos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') && ( $file = get_file($basepath . '/wp-tinymce.js.gz') ) ) {

	header('Content-Encoding: gzip');
	echo $file;
} else { */
	// Back compat. This file shouldn't be used if this condition can occur (as in, if gzip isn't accepted).
	?>
//this script is temporary, to fix toolbar parameter syntax
//this is for the post edit page
	document.addEventListener( 'DOMContentLoaded', function( e ) {
		if (tinyMCEPreInit && tinyMCEPreInit.mceInit) {
			for (var ed in tinyMCEPreInit.mceInit) {
				let hold = '';
				for (const key of ['toolbar1','toolbar2','toolbar3','toolbar4']) {
					if (tinyMCEPreInit.mceInit[ed][key]) {
						hold += tinyMCEPreInit.mceInit[ed][key].replace(/,/g, ' ') + ' |';
					}
					delete tinyMCEPreInit.mceInit[ed][key];
				}
				tinyMCEPreInit.mceInit[ed].toolbar = hold;
				tinyMCEPreInit.mceInit[ed].theme = 'silver';
			}
		}
	} );
//this is for other pages like Text Widget
	jQuery(document).on( 'wp-before-tinymce-init', function( event, init ) {
		for (var key in init) {
			if (key.startsWith('toolbar')) {
				init[key] = init[key].replace(/,/g, ' ');
			}
			if (key === ('plugins')) {
				init[key] = init[key].replace(/wplink/g, 'link');
				init[key] = init[key].replace(/colorpicker/g, '');
				init[key] = init[key].replace(/textcolor/g, '');
			}
		}
		init.theme = 'silver';
	} );
<?php
	echo get_file( $basepath . '/tinymce.min.js' );
//	echo get_file( $basepath . '/plugins/compat3x/plugin.min.js' );
	echo get_file( $basepath . '/plugins/compat4x/plugin.min.js' );
//}
exit;
