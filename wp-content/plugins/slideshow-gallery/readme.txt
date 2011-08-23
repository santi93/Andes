=== Slideshow Gallery ===
Contributors: Antonie Potgieter, Tribulant Software
Donate link: http://tribulant.com/
Tags: wordpress plugins, wordpress slideshow gallery, slides, slideshow, image gallery, images, gallery, featured content, content gallery, javascript, javascript slideshow, slideshow gallery
Requires at least: 2.9
Tested up to: 3.0.1
Stable tag: 1.1.1

Feature content in a JavaScript powered slideshow gallery showcase on your WordPress website

== Description ==

Feature content in a JavaScript powered slideshow gallery showcase on your WordPress website.

You can easily display multiple galleries throughout your WordPress website displaying your custom added slides or showing slides from WordPress posts.

The slideshow is flexible and all aspects can easily be configured.

Embedding or hardcoding the slideshow gallery is a breeze. To embed into a post/page, simply insert `[slideshow]` into its content with an optional `post_id` parameter to display the gallery images uploaded to that post/page `[slideshow post_id=123]`. You can also use an `exclude` parameter to exclude certain images by their order in the gallery (comma separated) from the slideshow like this `[slideshow post_id=123 exclude="3,7,9"]`. 

To hardcode into any PHP file of your WordPress theme, simply use `<?php if (class_exists('Gallery')) { $Gallery = new Gallery(); $Gallery -> slideshow($output = true, $post_id = null); }; ?>`.

== Installation ==

Installing the WordPress slideshow gallery plugin is very easy. Simply follow the steps below.

1. Extract the package to obtain the `slideshow-gallery` folder
1. Upload the `slideshow-gallery` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Configure the settings according to your needs through the 'Slideshow' > 'Configuration' menu
1. Add and manage your slides in the 'Slideshow' section
1. Put `[slideshow post_id="X" exclude=""]` to embed a slideshow with the images of a post into your posts/pages or use `[slideshow custom=1]` to embed a slideshow with your custom added slides or `<?php if (class_exists('Gallery')) { $Gallery = new Gallery(); $Gallery -> slideshow($output = true, $post_id = null); }; ?>` into your WordPress theme

== Frequently Asked Questions ==

= Can I display/embed multiple instances of the slideshow gallery? =

Yes, you can, but only one slideshow per page.

= How do I display the images uploaded to a post? =

You can embed a slideshow and show the images uploaded to a post with the `post_id` parameter. Like this `[slideshow post_id="123"]`.

= Can I exclude certain images from a post in the slideshow? =

Yes, you can use the `exclude` parameter to exclude post images by their order in the gallery (comma separated) like this `[slideshow post_id=123 exclude="2,4,8"]`.

== Screenshots ==

1. Slideshow gallery with thumbnails at the bottom.
2. Slideshow gallery with thumbnails turned OFF.
3. Slideshow gallery with thumbnails at the top.
4. Different styles/colors.
5. TinyMCE editor button to insert shortcodes.
6. Turn on Thickbox to show enlarged images in an overlay.

== Changelog ==

= 1.0 =
* Initial release of the WordPress Slideshow Gallery plugin

= 1.0.3 =
* ADDED: Default, English language file in the `languages` folder.
* ADDED: Configuration setting to turn On/Off resizing of images via CSS.
* ADDED: Webkit border radius in CSS for thumbnail images.
* ADDED: `post_id` parameter for the `[slideshow]` shortcode to display images from a post/page.
* IMPROVED: Plugin doesn't utilize PHP short open tags anymore.
* COMPATIBILITY: Removed `autoLoad` (introduced in PHP 5) parameter from `class_exists` function for PHP 4 compatibility.
* IMPROVED: Directory separator constant DS from DIRECTORY_SEPARATOR.

= 1.0.4 =
* COMPATIBILITY: WordPress 2.9
* FIXED: #fullsize z-index to keep below other elements such as drop down menus.

= 1.0.4 =
* IMPROVED: WordPress 2.9 sortable meta boxes.
* FIXED: `wp_redirect()` fatal error in dashboard.
* ADDED: TinyMCE editor button to quickly insert slideshows into posts/pages.
* ADDED: `exclude` parameter to use in conjunction with the `post_id` parameter to exclude attachments by order.
* CHANGED: Changed `#wrapper` in the HTML markup to `#slideshow-wrapper` due to some theme conflicts.

= 1.1 =
* ADDED: "THIS POST" added to the TinyMCE dialog to insert a shortcode for a slideshow of the current post
* IMPROVED: Some CSS improvements to the slideshow
* ADDED: Thickbox to show images in overlay. Can be turned On/Off
* FIXED: Fixed all thumbnails not preloading on first load
* FIXED: Slide HREF in IE
* ADDED: Spinner loading indicator to show the slideshow is loading up
* ADDED: "Link" column in the "Manage Slides" section
* FIXED: Load Thickbox on the 'Manage Slides' page for the enlargements
* ADDED: Ability to upload an image when saving a slide rather than specifying a URL
* ADDED: Row actions in the 'Manage Slides' section