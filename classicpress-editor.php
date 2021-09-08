<?php

/**
 * -----------------------------------------------------------------------------
 * Plugin Name: ClassicPress Editor - Experimental
 * Description: An integration of TinyMCE (v5) that brings a modern editing experience to ClassicPress while preserving the familiarity and function that users have grown to love. This plugin is not yet intended for production use.
 * Version: 1.0.0-alpha
 * Author: John Alarcon, Joy Reynolds, and ClassicPress Contributors
 * Author URI: https://www.classicpress.net
 * Text Domain: classicpress-editor
 * Domain Path: /languages
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

		// Suppress Tiny in certain contexts.
		if ( $this->suppress_editor() ) {
			return;
		}

		// Enqueue frontend assets.
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_public_assets' ] );

		// Ensure TinyMCE is loading from within this plugin, not core.
		add_filter( 'includes_url', [ $this, 'filter_tinymce_includes_url' ], 10, 2 );

		// Filter the TinyMCE config object.
		add_filter( 'tiny_mce_before_init', [ $this, 'filter_tinymce_init' ], 10, 2 );

		// Filter the plugins loaded by TinyMCE.
		add_filter( 'tiny_mce_plugins', [ $this, 'filter_tinymce_plugins' ], 11 );

		// Filter the buttons added to the TinyMCE editor.
		add_filter( 'mce_buttons', [ $this, 'filter_tinymce_buttons' ], 10, 2 );

		// Performant enqueuing for prism.js (for codesample TinyMCE plugin.)
		add_filter( 'the_content', [ $this, 'enqueue_prism_assets' ] );

	}

	public function filter_tinymce_includes_url( $url, $path ) {

		if ( strpos( $path, 'tinymce' ) !== false ) {
			$url = plugins_url( $path, __FILE__ );
		}

		return $url;

	}

	public function filter_tinymce_init( $mceInit, $editor_id ) {

		$mceInit['theme'] = 'silver'; //renaming silver folder to modern doesn't work
		$mceInit['height'] = 700 + 75; //height now includes menu
		$mceInit['min_height'] = 100 + 75;
		$mceInit['resize'] = true; //old value 'vertical' not available
		$mceInit['toolbar_location'] = 'top';
		$mceInit['toolbar_persist'] = true;
		$mceInit['custom_ui_selector'] = '.wp-editor-tools';

		return $mceInit;

	}

	public function filter_tinymce_plugins( $plugins ) {

		foreach ( array( 'wordpress', 'wplink', 'colorpicker', 'textcolor', 'wpautoresize' ) as $word ) {
			if ( ($i = array_search( $word, $plugins )) !== false ) {
				unset( $plugins[$i] );
			}
		}

		// $plugins[] = 'autoresize'; //while wpautoresize is not working

		return $plugins;

	}

	public function filter_tinymce_buttons( $mce_buttons, $editor_id ) {

		// This is how to add buttons.
		//$mce_buttons[] = 'code';
		//$mce_buttons[] = 'codesample';

		return $mce_buttons;

	}

	/**
	 * Don't load Tiny for comments editor, quick draft editor, other
	 */
	public function suppress_editor() {

		// Not on the admin side? Bail.
		if (!is_admin() || empty($GLOBALS['pagenow'])) {
			return false;
		}

		// Conditions for which to suppress the TinyMCE editor.
		if ($GLOBALS['pagenow'] === 'index.php') { // Dashboard quick draft
			if (isset($GLOBALS['plugin_page'])) {
				if ($GLOBALS['plugin_page'] === null) {
					return true;
				}
			}
		} else if (isset($GLOBALS['pagenow'])) {
			if ($GLOBALS['pagenow'] === 'comment.php') {
				if ($_GET['action'] === 'editcomment') { // Comment edit screen
					return true;
				}
			}
		}

		// Do not suppress editor.
		return false;

	}

	public function enqueue_public_assets() {

		/**
		 * The prism.js assets are only registered here. They are enqueued later
		 * on an as-needed basis in the enqueue_prism_assets() method which runs
		 * on the the_content filter.
		 */
		wp_register_script( 'classicpress-editor-syntax-highlighter', plugin_dir_url(__FILE__).'js/prism.js', [], time(), true );
		wp_register_style( 'classicpress-editor-syntax-highlighter',  plugin_dir_url(__FILE__).'css/prism.css', [], time() );

	}

	public function enqueue_prism_assets($content) {

		// If <pre lang="whatever" is found in $content, enqueue prism assets.
		if ( preg_match( '/<pre *(\w*? *= *["].*?["]|(\w*)*?)>/', $content ) === 1 ) {
			wp_enqueue_script( 'classicpress-editor-syntax-highlighter' );
			wp_enqueue_style( 'classicpress-editor-syntax-highlighter' );
		}

		// Return the possibly-amended content.
		return $content;

	}

}

// Make beautiful all the things.
new Editor;
