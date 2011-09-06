=== iSlidex ===
Contributors: Dukessa
Donate link: http://www.shambix.com/en/news/wordpress-plugin-islidex/
Tags: slideshow, slider, featured, slideshow, slider, carousel, showcase, islidex, captions, nivo, piecemaker, apple, gallery, timeline
Requires at least: 2.9
Tested up to: 3.2.1
Stable tag: trunk

== Description ==

= iSlidex is a Wordpress slideshow plugin that will display images taken from posts in a specific category. =

Completely automated once you set the number of slides you would like to feature, the size and the category from where iSlidex will pull the images from.
You can decide also whether to have nice semi-transparent captions, with the title of the post for each slide.
iSlidex comes with a widget, which can be set independently from the main slider, from the same settings page, however we do recommend the use of the plugin only inside big sidebars, in order to be displayed in the best way.
Every image is resized and cached automatically, and you dont have to worry about server load or manual image editing.
Also, we optimized the code for better SEO, so that every image has its alt and titles attributes and we have added compatibility with qTranslate plugin.
Islidex also comes with different slideshow themes to choose from!

Cross-browser compliant: Internet Explorer 7, Internet Explorer 8, FireFox 3+, Safari 4+, Chrome 4+, Opera 10+.

Wordpress 3.1+ compatible.

= Can I use it in a post/page to display only the images from that post/page? =

NO!
iSlidex is specifically built only to display 1 image per each post from a set category, not all the images in 1 post. We are thiking about adding this in the future, no need to request it.

= Problems making it work? =

If you can't make the plugin work, check out the Usage instructions and FAQ, ask in the forums so we can help you, instead of reporting it as broken.
If you have tried all the available solutions and/or you notice a bug, please contact us directly at info AT shambix.com


Credits to [TutorialZine](http://tutorialzine.com/2009/11/beautiful-apple-gallery-slideshow/) for the Apple slider.
Credits to [Brian Reavis](http://thirdroute.com/projects/captify/) for Captify.
Credits to [Tim McDaniels](http://www.darrenhoyt.com/2008/04/02/timthumb-php-script-released/) for TimThumb.
Credits to [Dev7Studios](http://nivo.dev7studios.com/) for the Nivo slider.
Credits to [ModularWeb](http://www.modularweb.net/piecemaker/) for the Piecemaker 3D slider.

== Installation ==

1. Upload the folder `islidex` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Set up the slider and widget through the iSlides settings page, that you can reach through the left 'Plugin' box

= Posts & Pages =

Copy and paste one of these codes inside your posts/pages, to display the slideshow:

* `[islidex]` (it will show on top of the post content)
* `<?php show_islidex(); ?>` inside your template

= Custom different slideshows =

This function lets you add as many iSlidex as you want across your site, all with different settings, indipendent from this page settings or other iSlidex in your site.

Please remember that you can have max 3 iSlidex in the same page, but they can't be of the same type (eg. you can have 1 Nivo widget + 1 Nivo standard + 1 custom Nivo, but you can't have 2 custom Nivos or 2 standard Apple or 2 widgets with same style).

Copy and paste `[islidex_custom cat=5 num=3 w=450 h=200 theme=1]` in your posts or pages. Remember to change the numbers depending on the values you prefer.
If you want to use it directly in the template code, use `<?php show_customislidex(5,3,450,200,1);?>`.

The numbers 5, 3, 450, 200, 1 are only an example here, you must change them depending on the values you prefer.
These numbers represent, in order: `cat`egory (where to get the posts from), max `num`ber of posts/slides (to show in iSlidex), `w`idth (of the slider), `h`eight (of the slider), `theme` (1 is Apple, 2 is Nivo, 3 is Piecemaker, 4 is Timeline).
Please replace the numbers with the settings you prefer, each time you want to add a new slider in a different location.
The paramater `cat=` can take also multiple categories, separated by comma. Just type for example `cat=3,5`.

= Set specific slide/thumb image =

If for some posts with several images, you want to set a specific image and/or thumb, add these custom fields: `islidex_slide` and `islidex_thumb` as the Field Name, and paste the image link as the Field Value.

= Custom CSS =

If you want to change the look and feel of the themes, feel free to do it by uploading a file named `islidex.css` inside your template folder.
You may want to add `!important` in your CSS statements, as the pre-compiled CSS in iSlidex could override your custom ones sometimes.

= NOTICE =
 
Please bare in mind that this is a free plugin, that the time to work on it is limited and that we do our best anyway to give everybody a cool free plugin.
We can't always add new features to the standard, custom and widget features of iSlidex altogether, sometimes we will have to improve only one of them or maybe two, and finish up in the next release.
We prefer to push small but important little updates and fixes rather than making you wait months for the smallest improvement.
So if something doesn't work as you expected (eg. the custom iSlidex doesn't still support the Greek theme) please don't be upset or report the plugin as broken, either use another theme, or wait for an update or use another plugin.
Thanks for understanding.

== Frequently Asked Questions ==

Feel free to [open a new thread](http://wordpress.org/tags/islidex) in Wordpress Forums with tag `islidex`.
You can also leave a comment in the official plugin post on [Shambix](http://www.shambix.com/news/wordpress-plugin-islidex) site.

= Can we use the featured image as the slider? =

Yes.
However you can still use `islidex_slide` and `islidex_thumb` with the full link to the image, to achieve the same purpose and to keep retrocompatibility.

= Is there a way to have more than one slideshow (eg. a different category for each slider on different pages)? =

Yes, using the custom iSlidex function (read above).

= Why images are not showing? =

There could be 4 likely reasons: 
* The images in your post are taken from another website, and in that case the script TimThumb that resizes and caches images for iSlidex will NOT render them (resulting in a red error cross or empty space) to prevent bandwith theft
* You did not upload that image from that post, but you are relinking it from another post. iSlidex specifically retrieves images that are attached/uploaded directly to/from a post, and not linked from somewhere else
* Make sure that the `cache` folder in islidex->js plugin folder is writable, in case you are not sure set the `cache` folder to permission 777 
* Another plugin is messing with jQuery, try to deactivate your plugin one by one to see which one is causing the issue (Lightview gives a known conflict).

= The slides are all over the place! =

If you see all the images/slides across the page, it means that another plugin is interfering with iSlidex, either because of the CSS or the Javascript.
To make sure of this, please deactivate all plugins but iSlidex, flush the cache and reload the page, if you can see iSlidex now, please try to figure out which plugin is causing the issue and contact the author. We made iSlidex in a way so that it loads scripts ONLY when strictly needed, which is in the page/post it's actually used, so that it wouldn't conflict with other plugins, but of course if other authors have not done the same, or if you need two plugins to co-exist in the same page and they conflict, you will need to fix the specific issue on your own.
If it's caused by the CSS, you can upload your own `islidex.css` in your template folder, which will override all of iSlidex CSSs with your custom one.

= I can't see the thumbnails =

Check your style.css and see if maybe the general rules for `ul` and `li` are not messing up the ones for iSlidex. It shouldn't happen after v.1.6, but if it does, please report it in the forum. Always bear in mind that some themes for Wordpress might affect iSlidex look, as well as other plugins you may have installed and that's not something we can control or offer free support for.

= Can I use several iSlidex in the same page? =

Yes, you can have MAX 1 of each possible setups (standard, widget, custom), in the same page (so max 3 iSlidex per page, regardless the theme).
You can't have 2 custom iSlidex in the same page, or 2 standard ones, or 2 widgets.

= Why can't I select Piecemaker for the widget? =

Because it requires more space than what you set in the options, to be visualized properly,  and usually widgets don't have that much space availability, therefore Piecemaker is disabled for widgets.

= Why can't I choose specific effects (Nivo, Piecemaker themes), nor captions, nor linked slides options,  in the custom iSlidex? =

For now, the custom iSlidex will follow the standard one for what concernes effects,  caption selection and linked slides options.
The reason is simple, this is a free plugin and we only have limited time to work on it.
In the next releases we might add more options.
However, not all options will be available, as for some themes this would require too many params in the shortcode/function, making it all quite complex for users to setup.

= How do I change the color of post titles and excerpt inside Piecemaker text box? =

You can either edit the file inside the plugin folder /themes/piecemaker/islidex_piecemaker.css or upload your own islidex.css to your wordpress template folder.

== Screenshots ==

1. Apple Theme
2. Nivo theme
3. Piecemaker theme
4. Timeline theme
5. Greek theme
6. How to add the custom fields inside posts

== Changelog ==

= 2.7 =
* Validated XHTML
* Fixed widget slides not linking to post
* Fixed some minor bugs

= 2.6 =
* NEW! Added auto advance option to custom function
* Fixed a bug in custom nivo slider
* Made some CSS !important (as some themes tend to f_ck up the slider)
* Piecemaker now takes less white space around the slider

= 2.5 =
* NEW! Added "Greek" to themes
* Fixed a bug in Apple custom slider thumbs alignment
* Made Nivo theme, the Timeline theme and Apple theme compatible with IE6 (except for some Nivo javascript effects that just won't work at all in IE6)

= 2.3 =
* Fixed bugs with custom slider
* Fixed bugs with Nivo theme

= 2.2 =
* NEW! Added "Linked Slides" options, the ability to choose whether to link slides to their posts or not
* Fixed layout bugs in Apple theme for IE6 & IE7
* Fixed bug with Nivo captions not showing

= 2.1 =
*NEW! Added "Timeline" to themes

= 2.0 =
* NEW! Added "Piecemaker" to themes
* NEW! Users can add custom Widget title
* NEW! Custom iSlidex can display multiple category posts
* NEW! Added jQuery noConflict
* NEW! Enqued jQuery
* NEW! Multiple iSlidex can cohexist in the same page
* Fixed bugs in widget
* Added Nivo theme for widget
* Added Nivo and Piecemaker to custom islidex
* Updated TimThumb to v.1.19
* Updated Settings panel

= 1.9.5 =
* NEW! Added "Featured Image" as slide
* NEW! Widget can follow the main slider style
* NEW! Widget and main slider can co-exist on the same page
* NEW! If post doesnt have any image at all, a Wordpress logo will be shown
* Fixed a permission issue in settings page
* Removed direction arrows in Nivo theme

= 1.9 =
* Fixed slide order misbehaviour
* Fixed whitespaces
* Added Nivo effect selection
* Fixed Apple style CSS
* Fixed Widget activation

= 1.8.1 =
* Fixed closing ?> and whitespace

= 1.8 =
* Added Themes capability
* Added Nivo Slider style
* Added support for qTranslate titles
* Fixed alt and title attribute
* iSlidex is now fully compatible with IE7, IE8, FF3, Safari4, Chrome4, Opera4.

= 1.7 =
* Added  Captify, to show a caption for every slide with post title
* Minor changes in Settings page

= 1.6 =
* Bugfix for posts
* Added [islidex_altern], [islidex_altern2] and [islidex_thesis] in case the normal shortcodes don't work
* Fixed the css as in some cases the template css would brake iSlidex

= 1.5 =
* Bugfix for more than 5 sliders in pages

= 1.4 =
* Bugfix on post slides

= 1.3 =
* Settings page fixes + more instructions
* Javascript only loads when iSlidex is actually in use (to prevent conflicts)
* Javascript now loads in the footer to speed up page load
* Quick fix for messed up alt attributes when the plugin qTranslate is active

= 1.2 =
* Settings page fixes
* Added custom different slideshows functionality

= 1.1 =
* Bugfix for pages shortcode

= 1.0 =
* Plugin release

== About ==

You can get the latest version also directly [HERE](http://plugins.trac.wordpress.org/browser/islidex/trunk)

= Who are we? =

Shambix is a Design & Marketing Consulting firm.

We create all kind of plugin, template and widget for Wordpress, among lots of other cool stuff for our clients.
This is a free plugin, so please be patient and kind, we will try to update it as much as we can.
We provide support for bugs but we don't give any free support for individual customization, if you need some specific work done for your website, contact us and we will get back to you with a quote and timeframe.

If you have any special request, feel free to contact us at info AT shambix.com

ENJOY! :)