<?php

/**
 * -----------------------------------------------------------------------------
 * Plugin Name: ClassicPress Editor - Experimental
 * Description: An integration of TinyMCE (v5) that brings a modern editing experience to ClassicPress while preserving the familiarity and function that users have grown to love. This plugin is not yet intended for production use.
 * Version: 1.0.0
 * Author: John Alarcon
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

		// Shall we, then?
		add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);

	}

	public function enqueue_admin_assets() {

		// Enqueue TinyMCE JS.
		wp_enqueue_script('classicpress-tinymce5', plugin_dir_url(__FILE__).'scripts/tinymce5/tinymce.min.js');

		// Enqueue CP-centric TinyMCE JS.
		wp_enqueue_script('classicpress-editor',   plugin_dir_url(__FILE__).'scripts/classicpress-editor.js', ['classicpress-tinymce5']);

		// Enqueue CP-centric TinyMCE CSS.
		wp_enqueue_style('classicpress-editor',    plugin_dir_url(__FILE__).'styles/classicpress-editor.css');

	}

}

new Editor;