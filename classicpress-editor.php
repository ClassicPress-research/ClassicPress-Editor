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

		// Suppress Tiny in certain contexts.
		if ($this->suppress_editor()) {
			return;
		}

		// Disable TinyMCE v4.
		add_filter('user_can_richedit', '__return_false');

		// Enqueue backend assets.
		add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);

		// Enqueue frontend assets.
		add_action('wp_enqueue_scripts', [$this, 'enqueue_public_assets']);

		// Performant enqueuing for prism.js (for codesample TinyMCE plugin.)
		add_filter('the_content', [$this, 'enqueue_prism_assets']);

		add_action('admin_print_footer_scripts', [$this, 'window_unload_error_fix']);

	}

	/**
	 * Don't load Tiny for comments editor, quick draft editor, other
	 */
	public function suppress_editor() {

		// Not in a relevant view? Bail.
		if (!is_admin() || empty($GLOBALS['pagenow'])) {
			return false;
		}

		// Conditions for which to suppress the TinyMCE editor.
		if ($GLOBALS['pagenow'] === 'index.php') { // Dashboard quick draft
			if ($GLOBALS['plugin_page'] === null) {
				return true;
			}
		} else if ($GLOBALS['pagenow'] === 'comment.php') {
			if ($_GET['action'] === 'editcomment') { // Comment edit screen
				return true;
			}
		}

		// Do not suppress editor.
		return false;

	}

	public function window_unload_error_fix() {
		?>
		<script>
			jQuery(document).ready(function($) {

				// Check screen
				if(typeof window.wp.autosave === 'undefined')
					return;

				// Data Hack
				var initialCompareData = {
					post_title: $( '#title' ).val() || '',
					content: $( '#content' ).val() || '',
					excerpt: $( '#excerpt' ).val() || ''
				};

				var initialCompareString = window.wp.autosave.getCompareString(initialCompareData);

				// Fixed postChanged()
				window.wp.autosave.server.postChanged = function() {

					var changed = false;

					// If there are TinyMCE instances, loop through them.
					if (window.tinymce) {
						window.tinymce.each(['content', 'excerpt'], function(field) {
							var editor = window.tinymce.get(field);

							if ((editor && editor.isDirty()) || ($('#'+field ).val() || '') !== initialCompareData[field]) {
								changed = true;
								return false;
							}

						});

						if (($('#title' ).val() || '') !== initialCompareData.post_title) {
							changed = true;
						}

						return changed;
					}

					return window.wp.autosave.getCompareString() !== initialCompareString;

				}
			});
		</script>
		<?php
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
