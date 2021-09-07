// Full documentation at https://www.tiny.cloud/docs/

// This wrapper may not be required.
jQuery(document).ready(function($) {

	// The code here was taken from https://www.tiny.cloud/docs/demo/full-featured/#fullfeaturednon-premiumplugins

	// Take user display preferences into account.
	var useDarkMode = window.matchMedia('(prefers-color-scheme: dark)').matches;

	// Define the editor settings.
	tinymce.init({
		selector: '#content',
		plugins:
			// Probably included plugins.
			'advlist anchor autolink autoresize charmap code codesample cpcode ' +
			'directionality fullscreen help hr importcss link lists noneditable ' +
			'paste searchreplace table textpattern visualblocks visualchars ' +
			'wordcount ' +
			// Everything else
			'print preview autosave save image media template pagebreak nonbreaking toc insertdatetime imagetools quickbars emoticons',
		toolbar1: // Possible toolbar1 config
			'visualblocks | formatselect | bold italic strikethrough | superscript subscript |  ' +
			'forecolor backcolor | removeformat |  bullist numlist |  link anchor hr blockquote charmap | ' +
			'fullscreen',
		toolbar2: // Possible toolbar1 config
			'alignleft aligncenter alignright | outdent indent | code codesample | ' +
			'searchreplace table | undo redo | help',
		toolbar3: // Everything else
			'underline | fontselect | fontsizeselect | alignjustify | pagebreak | emoticons | fullscreen | preview | save | print | insertfile | image | media | template | ltr rtl',
		toolbar_mode: 'wrap',
		toolbar_sticky: true,
		toolbar_sticky_offset: 32,
		contextmenu: false,
		menubar: false,
		height: 600,
		min_height: 475,
		max_height: 1000,
		browser_spellcheck: true,
		skin: useDarkMode ? 'oxide-dark' : 'oxide',
	});

});
