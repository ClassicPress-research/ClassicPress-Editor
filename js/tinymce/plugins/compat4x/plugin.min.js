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

	function patchEditor(editor) {
		editor.addButton = function (name, settings) {
			for (var key in settings) {
				if (key.toLowerCase() === "cmd") {
					settings.onAction = function () {
						editor.execCommand(settings.key);
					};
				}
				if (key.toLowerCase() === "onclick") {
					settings.onAction = settings.key;
				}
				if (key.toLowerCase() === "onpostrender") {
					settings.onSetup = settings.key
				}
			}

		return editor.ui.registry.addButton.call(this, name, settings);
	};

	editor.addContextToolbar = function (name, spec) {
		for (var key in spec) {
			if (key.toLowerCase() === "onclick") {
				spec.onAction = spec.key;
			}
		}
		return editor.ui.registry.addContextToolbar.call(this, name, spec);
	};

	editor.addMenuItem = function (name, spec) {
		for (var key in spec) {
			if (key.toLowerCase() === "onclick") {
				spec.onAction = spec.key;
			}
		}
		return editor.ui.registry.addMenuItem.call(this, name, spec);
	};

	editor.addSidebar = function (name, spec) {
		return editor.ui.registry.addSidebar.call(this, name, spec);
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
			iframeElm = editor.getContentAreaContainer().querySelector('iframe');
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
			DOM$1.setStyle(iframeElm, 'height', height);
			editor.fire('ResizeEditor');
		};
		
		//Put 4.x classes on things used by editor-expand.js
		var el = editor.getContainer();
		el.querySelector('.tox-toolbar-overlord').classList.add('mce-toolbar-grp');
		el.querySelector('.tox-edit-area').classList.add('mce-edit-area');
		el.querySelector('.tox-statusbar').classList.add('mce-statusbar');
		if (el.querySelector('.tox-menubar')) {
			el.querySelector('.tox-menubar').classList.add('mce-menubar');
		}
		
		//Add override rules for 5.x classes
		var hStyle = document.createElement( 'style' );
		document.head.appendChild( hStyle );
		var css = '.tox-fullscreen #wp-content-wrap .mce-menubar,\
.tox-fullscreen #wp-content-wrap .mce-toolbar-grp,\
.tox-fullscreen #wp-content-wrap .mce-edit-area,\
.tox-fullscreen #wp-content-wrap .mce-statusbar {\
	position: static !important;\
	width: auto !important;\
	padding: 0 !important;\
}\
.tox-fullscreen #wp-content-wrap .mce-statusbar {\
	visibility: visible !important;\
}\
.tox-fullscreen #wp-content-wrap .tox-tinymce .mce-wp-dfw {\
	display: none;\
}\
.post-php.mce-fullscreen #wpadminbar,\
.tox-fullscreen #wp-content-wrap .mce-wp-dfw {\
	display: none;\
}';
			hStyle.innerHTML = css;
			
			//This moves the textarea to the bottom, like it is on 4.x
			//el.parentNode.appendChild(document.querySelector('.wp-editor-area'));
		});
	}
	
	tinymce.on('SetupEditor', function (e) {
		patchEditor(e.editor);
	});


	tinymce.PluginManager.add("compat4x", patchEditor);

})(tinymce);