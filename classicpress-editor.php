<?php
/**
 * -----------------------------------------------------------------------------
 * Plugin Name: ClassicPress Editor update - Experimental
 * Description: Update to TinyMCE version 5.9.  This plugin is not yet intended for production use.
 * Version: 1.0.5-alpha
 * Author: John Alarcon, Joy Reynolds, and ClassicPress Contributors
 * -----------------------------------------------------------------------------
 * This is free software released under the terms of the General Public License,
 * version 2, or later. It is distributed WITHOUT ANY WARRANTY; without even the
 * implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Full
 * text of the license is available at https://www.gnu.org/licenses/gpl-2.0.txt.
 * -----------------------------------------------------------------------------
 * Copyright 2021, John Alarcon
 * -----------------------------------------------------------------------------
 */

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

global $tinymce_version;
$tinymce_version = '591-20210827';

// Ensure scripts are loading from within this plugin, not core.
add_filter( 'includes_url', 'try_tinymce5_tinymce_includes_url', 10, 2 );
add_filter( 'script_loader_src', 'try_tinymce5_editor_script_url', 10, 2 );
function try_tinymce5_tinymce_includes_url( $url, $path ) {
	if ( strpos( $path, 'tinymce' ) !== false ) {
		$url = plugins_url( $path, __FILE__ );
	}
	return $url;
}

function try_tinymce5_editor_script_url( $src, $handle ) {
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

// Visual Mode: filter the TinyMCE config object.
add_filter( 'tiny_mce_before_init', 'try_tinymce5_tinymce_init', 11, 2 );
function try_tinymce5_tinymce_init( $mceInit, $editor_id ) {
	$mceInit['theme'] = 'silver'; //renaming silver folder to modern doesn't work
//	$mceInit['height'] = 300 + 75; //height now includes UI
//	$mceInit['min_height'] = 100 + 75;
//	$mceInit['resize'] = true; //old value 'vertical' for boolean option

//	$mceInit['skin'] = 'darkgray';  //core loads lightgray
	$mceInit['toolbar_mode'] = 'sliding';  //still testing best option
	$mceInit['toolbar_location'] = 'top'; //auto was added and set as the default in TinyMCE 5.3

	$mceInit['custom_ui_selector'] = '.wp-editor-tools';
	return $mceInit;
}

// Visual Mode: filter core plugins.
add_filter( 'tiny_mce_plugins', 'try_tinymce5_tinymce_plugins', 11 );
function try_tinymce5_tinymce_plugins( $plugins ) {
	// colorpicker and textcolor were made part of 5.x core
	foreach ( array( 'wplink', 'colorpicker', 'textcolor' ) as $word ) {
		if ( ($i = array_search( $word, $plugins )) !== false ) {
			unset( $plugins[$i] );
		}
	}
	$plugins[] = 'searchreplace'; //add new feature, TODO: translations handled?
	$plugins[] = 'link'; //while wplink is not working
	return $plugins;
}

// Visual Mode: filter second row of buttons.
add_filter( 'mce_buttons_2', 'try_tinymce5_mce_buttons_2' );
function try_tinymce5_mce_buttons_2( $buttons ) {
	$hold = array_pop( $buttons );
	array_push( $buttons, 'searchreplace', $hold );
	return $buttons;
}

// Text Mode: filter the config object.
add_filter( 'teeny_mce_before_init', 'try_tinymce5_teenymce_init', 11, 2 );
function try_tinymce5_teenymce_init( $mceInit, $editor_id ) {
	// TinyMCE 5.5+ This option disables the automatic show and hide behavior of the toolbar and menu bar for inline editors
	$mceInit['toolbar_persist'] = true;  //inline editor - needed for plugins?
}

// Text Mode: filter core plugins.
add_filter( 'teeny_mce_plugins', 'try_tinymce5_teenymce_plugins', 11 );
function try_tinymce5_teenymce_plugins( $plugins ) {
	// colorpicker and textcolor were made part of 5.x core
	foreach ( array( 'colorpicker', 'textcolor' ) as $word ) {
		if ( ($i = array_search( $word, $plugins )) !== false ) {
			unset( $plugins[$i] );
		}
	}
	return $plugins;
}
