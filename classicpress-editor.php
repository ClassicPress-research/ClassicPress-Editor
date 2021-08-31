<?php

/**
 * -----------------------------------------------------------------------------
 * Plugin Name: ClassicPress Editor - Experimental
 * Description: An integration of TinyMCE (v5) that brings a modern editing experience to ClassicPress while preserving the familiarity and function that users have grown to love. This plugin is not yet intended for production use.
 * Version: 1.0.0
 * Author: John Alarcon & ClassicPress Contributors
 * Author URI: https://forums.classicpress.net/u/code_potent
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

if (!defined('ABSPATH')) {
	die();
}

class Editor {

	public function __construct() {

		// Disable TinyMCE v4.
		add_filter('user_can_richedit', '__return_false');

		// Enqueue backend assets.
		add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);

		// Enqueue frontend assets.
		add_action('wp_enqueue_scripts', [$this, 'enqueue_public_assets']);

		// Performant enqueuing for prism.js (for codesample TinyMCE plugin.)
		add_filter('the_content', [$this, 'enqueue_prism_assets']);

	}

	public function enqueue_admin_assets() {

		// Enqueue TinyMCE JS.
		wp_enqueue_script('classicpress-tinymce5', plugin_dir_url(__FILE__).'scripts/tinymce5/tinymce.min.js');

		// Enqueue CP-centric TinyMCE JS.
		wp_enqueue_script('classicpress-editor',   plugin_dir_url(__FILE__).'scripts/classicpress-editor.js', ['classicpress-tinymce5']);

		// Enqueue CP-centric TinyMCE CSS.
		wp_enqueue_style('classicpress-editor',    plugin_dir_url(__FILE__).'styles/classicpress-editor.css');

	}

	public function enqueue_public_assets() {

		/**
		 * The prism.js assets are only registered here. They are enqueued later
		 * on an as-needed basis in the enqueue_prism_assets() method which runs
		 * on the the_content filter.
		 */
		wp_register_script('classicpress-editor-syntax-highlighter', plugin_dir_url(__FILE__).'scripts/prism.js', [], time(), true);
		wp_register_style('classicpress-editor-syntax-highlighter',  plugin_dir_url(__FILE__).'styles/prism.css', [], time());

	}

	public function enqueue_prism_assets($content) {

		// If <pre lang="whatever" is found in $content, enqueue prism assets.
		if (preg_match('/<pre *(\w*? *= *["].*?["]|(\w*)*?)>/', $content) === 1) {
			wp_enqueue_script('classicpress-editor-syntax-highlighter');
			wp_enqueue_style('classicpress-editor-syntax-highlighter');
		}

		// Return the possibly-amended content.
		return $content;

	}

}

// Make beautiful all the things.
new Editor;