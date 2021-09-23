<?php

/**
 * -----------------------------------------------------------------------------
 * Plugin Name: ClassicPress Editor - Experimental
 * Description: An integration of TinyMCE version 5.9.  This plugin is not yet intended for production use.
 * Version: 1.0.2-alpha
 * Author: John Alarcon, Joy Reynolds, and ClassicPress Contributors
 * Author URI: https://www.classicpress.net
 * Text Domain: classicpress-editor
 * -----------------------------------------------------------------------------
 * This is free software released under the terms of the General Public License,
 * version 2, or later. It is distributed WITHOUT ANY WARRANTY; without even the
 * implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Full
 * text of the license is available at https://www.gnu.org/licenses/gpl-2.0.txt.
 * -----------------------------------------------------------------------------
 * Copyright 2021, John Alarcon
 * -----------------------------------------------------------------------------
 */

namespace ClassicPress\TinyMce5;

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

class Editor {

	public function __construct() {

		global $tinymce_version;
		$tinymce_version = '591-20210827';

		// Ensure scripts are loading from within this plugin, not core.
		add_filter( 'includes_url', [ $this, 'filter_tinymce_includes_url' ], 10, 2 );
		add_filter( 'script_loader_src', [ $this, 'filter_editor_script_url' ], 10, 2 );

		// Visual Mode: filter the TinyMCE config object.
		add_filter( 'tiny_mce_before_init', [ $this, 'filter_tinymce_init' ], 10, 2 );

		// Visual Mode: filter core plugins.
		add_filter( 'tiny_mce_plugins', [ $this, 'filter_tinymce_plugins' ], 11 );

		// Visual Mode: filter second row of buttons.
		add_filter( 'mce_buttons_2', [ $this, 'filter_mce_buttons_2' ] );

	}

	public function filter_tinymce_includes_url( $url, $path ) {

		if ( strpos( $path, 'tinymce' ) !== false ) {
			$url = plugins_url( $path, __FILE__ );
		}

		return $url;

	}
	
	public function filter_editor_script_url( $src, $handle ) {
		if ( $handle === 'editor-expand' ) {
			$src = plugins_url( 'js/editor-expand.js', __FILE__ );
		}
		if ( $handle === 'post' ) {
			$src = plugins_url( 'js/post.js', __FILE__ );
		}
		if ( $handle === 'editor' ) {
			$src = plugins_url( 'js/editor.js', __FILE__ );
		}
		return $src;
	}

	public function filter_tinymce_init( $mceInit, $editor_id ) {

		$mceInit['theme'] = 'silver'; //renaming silver folder to modern doesn't work
//		$mceInit['height'] = 700 + 75; //height now includes menu
//		$mceInit['min_height'] = 100 + 75;		
//		$mceInit['resize'] = true; //old value 'vertical' 
		
		$mceInit['custom_ui_selector'] = '.wp-editor-tools';

		return $mceInit;

	}

	public function filter_tinymce_plugins( $plugins ) {

		foreach ( array( 'wplink', 'colorpicker', 'textcolor' ) as $word ) {
			if ( ($i = array_search( $word, $plugins )) !== false ) {
				unset( $plugins[$i] );
			}
		}
		$plugins[] = 'searchreplace'; //add new feature, needs translations handled
		$plugins[] = 'link'; //while wplink is not working
		return $plugins;

	}

	public function filter_mce_buttons_2( $buttons ) {
		$hold = array_pop( $buttons );
		array_push( $buttons, 'searchreplace', $hold );
		return $buttons;
	}

}

new Editor;
