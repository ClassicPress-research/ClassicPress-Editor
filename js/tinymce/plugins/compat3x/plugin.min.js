/**
 * plugin.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2017 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

/*global tinymce:true, console:true */
/*eslint no-console:0, new-cap:0 */
/**
 * This plugin adds missing events form the 4.x API back. Not every event is
 * properly supported but most things should work.
 *
 * Unsupported things:
 *  - No editor.onEvent
 *  - Can't cancel execCommands with beforeExecCommand
 */
//////////////////////////////////////////////////////
////// !! The compat3x has been removed and the compat4x substituted.
////// !! In order to ensure that compat4x is loaded in all cases, its code is here.
/**
 * plugin.js
 *
 * Released under LGPL License.
 */

/*global tinymce:true, console:true */
/*eslint no-console:0, new-cap:0 */

/**
 * This plugin adds aliases from 4.x to 5.x and 4.x class names.
 *
 */
(function (tinymce) {

	function patchEditor4(editor) {
		var originalAddButton = editor.addButton;
		editor.addButton = function (name, settings) {
			var classes = 'mce-ico mce-i-' + name;
			var wrap = 'mce-widget mce-btn'; //4.x wrapper has these classes
			for (var key in settings) {
				// 5.x removed cmd option
				if (key.toLowerCase() === "cmd") {
					settings.onAction = function () {
						editor.execCommand(settings[key]);
					};
				}
				// 4.x icon is a class name, 5.x icon is a name in icon pack
				if (key.toLowerCase() === "icon") {
					classes = 'mce-ico mce-i-' + settings[key];
					delete settings[key];
				}
				// 4.x classes is ignored in 5.x
				if (key.toLowerCase() === "classes") {
					wrap = 'mce-' + settings[key].replaceAll(' ', ' mce-');
				}
				// 4.x title is ignored in 5.x
				if (key.toLowerCase() === "title") {
					settings.tooltip = settings[key];
				}
				// 4.x image is ignored in 5.x
				if (key.toLowerCase() === "image") {
					classes = 'mce-ico mce-i-none' + '" style="background-image: url(\'' + settings[key] + '\')';
				}
				// 4.x onclick is 5.x onAction
				if (key.toLowerCase() === "onclick") {
					settings.onAction = settings[key];
				}
				// 4.x onpostrender is called once, 5.x onSetup is called each time it is created
				if (key.toLowerCase() === "onpostrender") {
					settings.onSetup = settings[key]
				}
			}
			// use empty text option to make 4.x CSS work
			if (! settings.hasOwnProperty('text') ) {
				settings['text'] = '<span class="'+ wrap +'"><i class="'+ classes +'"></i></span>';
			}
		console.warn('button '+name+': TinyMCE 4.x editor.addButton is 5.x editor.ui.registry.addButton');
		if (! settings.hasOwnProperty('onAction') ) {
			originalAddButton(name, settings); //this should throw an error
		}
		editor.ui.registry.addButton(name, settings);
	};

	var originalAddMenuItem = editor.addMenuItem;
	editor.addMenuItem = function (name, spec) {
		for (var key in spec) {
			// 5.x removed cmd option
			if (key.toLowerCase() === "cmd") {
				spec.onAction = function () {
					editor.execCommand(spec[key]);
				};
			}
			// 4.x onclick is 5.x onAction
			if (key.toLowerCase() === "onclick") {
				spec.onAction = spec[key];
			}
			// 4.x onpostrender is called once, 5.x onSetup is called each time it is created
			if (key.toLowerCase() === "onpostrender") {
				spec.onSetup = spec[key];
			}
		}
		console.warn('menuItem '+name+': TinyMCE 4.x editor.addMenuItem is 5.x editor.ui.registry.addMenuItem');
		if (! spec.hasOwnProperty('onAction') ) {
			originalAddMenuItem(name, spec); //this should throw an error
		}
		editor.ui.registry.addMenuItem(name, spec);
	};

	editor.on('init', function (e) {
		//Copy resizeTo function from 4.x Modern theme
		editor.theme.resizeTo = function (width, height) {
			var global$3 = tinymce.util.Tools.resolve('tinymce.dom.DOMUtils');
			var DOM$1 = global$3.DOM;
			var getSize = function (elm) {
				return {
					width: elm.clientWidth,
					height: elm.clientHeight
				};
			};
			var getMinWidth = function (editor) {
				return editor.getParam('min_width', 100, 'number');
			};
			var getMinHeight = function (editor) {
				return editor.getParam('min_height', 100, 'number');
			};
			var getMaxWidth = function (editor) {
				return editor.getParam('max_width', 65535, 'number');
			};
			var getMaxHeight = function (editor) {
				return editor.getParam('max_height', 65535, 'number');
			};
			var containerElm, iframeElm, containerSize, iframeSize;
			containerElm = editor.getContainer();
			iframeElm = editor.iframeElement;
			containerSize = getSize(containerElm);
			iframeSize = getSize(iframeElm);
			if (width !== null) {
				width = Math.max(getMinWidth(editor), width);
				width = Math.min(getMaxWidth(editor), width);
				DOM$1.setStyle(containerElm, 'width', width + (containerSize.width - iframeSize.width));
				DOM$1.setStyle(iframeElm, 'width', width);
			}
			height = Math.max(getMinHeight(editor), height);
			height = Math.min(getMaxHeight(editor), height);
			//added next line for 5.x
			DOM$1.setStyle(containerElm, 'height', height);
	//		DOM$1.setStyle(iframeElm, 'height', height);
			editor.fire('ResizeEditor');
		};

		//Put 4.x classes on things used by editor-expand.js
		var el = editor.getContainer();
		el.classList.add('mce-tinymce'); // 5.x uses tox-tinymce
		el.querySelector('.tox-toolbar-overlord,.tox-toolbar').classList.add('mce-toolbar-grp', 'mce-toolbar');
		el.querySelector('.tox-edit-area').classList.add('mce-edit-area');
		el.querySelector('.tox-statusbar').classList.add('mce-statusbar');
		if (el.querySelector('.tox-menubar')) {
			el.querySelector('.tox-menubar').classList.add('mce-menubar');
		}

		});
	}

	tinymce.on('SetupEditor', function (e) {
		patchEditor4(e.editor);
	});


	tinymce.PluginManager.add("compat4x", patchEditor4);

})(tinymce);

//this script is temporary, to fix toolbar parameter syntax
//this is for the post edit page
	document.addEventListener( 'DOMContentLoaded', function( e ) {
		if (tinyMCEPreInit && tinyMCEPreInit.mceInit) {
			for (var ed in tinyMCEPreInit.mceInit) {
				for (const key of ['toolbar1','toolbar2','toolbar3','toolbar4']) {
					if (tinyMCEPreInit.mceInit[ed][key]) {
						tinyMCEPreInit.mceInit[ed][key] = tinyMCEPreInit.mceInit[ed][key].replace(/,/g, ' ');
					}
				}
				tinyMCEPreInit.mceInit[ed].theme = 'silver';
				if (tinyMCEPreInit.mceInit[ed].skin === 'lightgray' &&
					 window.matchMedia('(prefers-color-scheme: dark)').matches) {
					tinyMCEPreInit.mceInit[ed].skin = 'darkgray';
				}
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
		if (init.skin === 'lightgray' &&
		 window.matchMedia('(prefers-color-scheme: dark)').matches) {
			init.skin = 'darkgray';
		}
} );
