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
 * This plugin adds aliases from 4.x to 5.x.
 *
 */
(function (tinymce) {

  function patchEditor(editor) {

    editor.addButton = function (name, settings) {
      for (var key in settings) {
        if (key.toLowerCase() === "cmd") {
          settings.onAction = function () {
 				   editor.execCommand(settings.cmd);
  				};
        }
        if (key.toLowerCase() === "onclick") {
          settings.onAction = settings.onclick;
        }
      }

      return editor.ui.registry.addButton.call(this, name, settings);
    };

    editor.addContextToolbar = function (name, spec) {
      for (var key in spec) {
        if (key.toLowerCase() === "onclick") {
          settings.onAction = settings.onclick;
        }
      }
      return editor.ui.registry.addContextToolbar.call(this, name, spec);
    };

    editor.addMenuItem = function (name, spec) {
      for (var key in spec) {
        if (key.toLowerCase() === "onclick") {
          settings.onAction = settings.onclick;
        }
      }
      return editor.ui.registry.addMenuItem.call(this, name, spec);
    };

    editor.addSidebar = function (name, spec) {
      return editor.ui.registry.addSidebar.call(this, name, spec);
    };
	}

  tinymce.on('SetupEditor', function (e) {
    patchEditor(e.editor);
  });

  tinymce.PluginManager.add("compat4x", patchEditor);

})(tinymce);
