### ClassicPress Editor

___

#### This is an experimental plugin and should not be used in production environments!

___

**NOTE**: There won't be a pull request for every single change; some changes may be committed directly to the `main` branch. See the [commit history](https://github.com/ClassicPress-research/ClassicPress-Editor/commits/main) to keep up with the changes.

Here is a list of [items marked TODO](https://github.com/ClassicPress-research/ClassicPress-Editor/search?q=TODO).
___

This plugin aims to bring TinyMCE version 5.x to the ClassicPress editor which is currently 4.8.0.

TinyMCE is used on the post editor page, the Text Widget, and anywhere a plugin adds it (like metaboxes or taxonomy descriptions or user biography).

The Code Reference shows only the [PHP side of things](https://docs.classicpress.net/reference/functions/wp_editor/), which includes using quicktags.js also. The Text Widget (and perhaps some plugins) sets it up from Javascript (see wp-admin/js/text-widget.js).

Please install the plugin and give it a try. Don't be gentle. Put it through the paces. Try weird things. Actively try to break it. And then report your findings [here](https://github.com/ClassicPress-research/ClassicPress-Editor/issues)!
The changes mentioned in the [migration document](https://www.tiny.cloud/docs/migration-from-4x/) are of particular interest.
