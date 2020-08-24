=== Lazy Load for Videos ===
Contributors: kevinweber
Donate link: https://www.kweber.com/donate/LazyLoadVideos/
Tags: youtube, vimeo, performance, seo, admin, plugin, content, video, mobile, lazy load, privacy
Requires at least: 4.5
Tested up to: 5.5.1
Stable tag: 2.9.0
License: GPL v3
License URI: https://www.gnu.org/copyleft/gpl.html

Speed up your site by replacing embedded Youtube and Vimeo videos with a clickable preview image. Visitors simply click on the image to play the video.

== Description ==

This plugin improves page load times and increases your Google PageSpeed Score. It replaces embedded Youtube and Vimeo videos with a clickable preview image.
By loading videos only when the user clicks on the preview image, no unnecessary JavaScript is loaded. Especially on sites with many embedded videos this will make your visitors happy. Additionally, all Youtube videos are loaded in a privacy-enhanced mode using the "https://www.youtube-nocookie.com" embed URL.

This plugin works for your existing YouTube and Vimeo blocks. No vendor lock-in and no custom shortcodes: Easily turn the plugin on and off anytime.

Demo on the developer’s website: [www.kweber.com/lazy-load-videos/](https://www.kweber.com/lazy-load-videos/)

You want to enhance this plugin? Please [contribute on Github](https://github.com/kevinweber/lazy-load-for-videos).

= Some additional features: =
* Display video titles on preview images
* Pre-roll and post-roll advertisements: Convert all videos into a playlist and automatically add your corporate video, product teaser or another video advertisement to every video. (Great for branding and video ads!)
* Hide annotations such as "subscribe to channel" to avoid distractions
* Add custom CSS via the plugin’s admin panel
* Choose custom colour for your Vimeo player
* Hide controls from Youtube player
* Hide information like the video title and uploader when the video starts playing
* Even lazy load videos in text widgets (Youtube only)
* Choose between thumbnail sizes (standard or cover)
* Choose from several play button styles
* Choose the traditional red or the alternative white progress bar for the Youtube video player
* Don't show related videos at the end of your videos
* Works with WordPress Multisite and many plugins such as TablePress

= Future features: =
* Track how often the videos have been loaded with Google Analytics
* ... YOU want a new feature RIGHT NOW? Please implement it yourself and [contribute on Github](https://github.com/kevinweber/lazy-load-for-videos), and I'll publish your enhancements to the official WordPress directory.


= Translators =
* Serbian (sr_RS) - [Ogi Djuraskovic](//firstsiteguide.com/)
* Spanish (es_ES) - [Carlos Villavicencio](http://po5i.github.io/)

If you have created your own language pack, or have an update of an existing one, you can [send me](https://www.kweber.com/contact/) your gettext PO and MO so that I can bundle it into my plugin. You can download the latest POT file [from here](https://plugins.svn.wordpress.org/lazy-load-for-videos/trunk/languages/lazy-load-for-videos.pot).


== Installation ==

Upload Lazy Load for Videos into you plugin directory (/wp-content/plugins/) and activate the plugin through the 'Plugins' menu in WordPress.

When you had already published videos before you have activated Lazy Load for Videos, update all posts by clicking the "Update Posts" button below the "Save Changes" button.

You may have to clean the website's and browser's cache.

If you don't see a preview image instead of the Youtube/Vimeo video, open the post editor and update/save the post again or even update all posts using the above mentioned "Update Posts" button.

Optionally: Sign up to the Lazy Load for Videos newsletter to get notified about major updates.

NOTICE - this is important to make your videos work as expected:
Easily insert the URL to your content (e.g. Youtube video) into a post or page. The URL must be on its own line and must not be hyperlinked. "WordPress will automatically turn [the URL] into a YouTube embed when the post is viewed." (Source: https://codex.wordpress.org/Embeds)
Inserting a Youtube iframe (instead of the plain URL) is deprecated and not supported by Lazy Load for Videos.


== Frequently Asked Questions ==

Search for keywords using "STRG + F" keyboard shortcut (Mac: "CMD + F").

= Where can I see this plugin in use? =
For example, on [www.kweber.com/lazy-load-videos/](https://www.kweber.com/lazy-load-videos/).

= Which video platforms are supported? =
Videos from the biggest video platform, Youtube (https://youtube.com), and Vimeo (https://vimeo.com) are supported.

= The plugin isn't working with Jetpack... =
"Jetpack" by WordPress.com offers some useful extensions. Only one of them makes Lazy Load for Videos break – the "Shortcode Embeds" extension. So simply disable the extension. (In order to see a "Deactivate" button for "Shortcode Embeds" on the Jetpack's extension overview, you must click on "Learn More".)

= Does the Lazy Load for Videos plugin work when there is more than one video on the same page? =
Yes. The plugin works on single posts and pages as well as on archive pages with several posts and videos.

= Should I use the Lazy Load for Videos plugin? =
Yes!

= Why? =
* If you want to speed your site up, this plugin is for you. Especially on sites with many embedded videos this plugin is gold. There are manz reasons why you should make your site faster, see next question.
* The plugin is really lightweight, has no dependencies and does neither blow your performance nor your database up.
* By loading the videos only when the user clicks on the preview image, no unnecessary JavaScript is loaded. You may know this issue: (Defer) Parsing of JavaScript. "In order to load a page, the browser must parse the contents of all <script> tags, which adds additional time to the page load. By minimizing the amount of JavaScript needed to render the page, and deferring parsing of unneeded JavaScript until it needs to be executed, you can reduce the initial load time of your page." (Source: https://developers.google.com/speed/docs/best-practices/mobile#DeferParsingJS)
* Video preview and play button are scalable and optimized for mobile devices.
* Finally, Lazy Load for Videos is really easy to use. Simply upload and activate the plugin.

= Why are a faster website and a higher Google PageSpeed Score great? =
* Search engines, especially Google, love speedy sites! So you will be higher ranked in search results.
* Consequently, you get more visitors.
* Fast sites lead to higher visitor engagement and returning visitors.
* Faster sites increase conversions.
* Potentially more $$ for your business!

= How can I calculate my Google PageSpeed Score? =
PageSpeed Insights is a service by Google to help you optimize your site's performance. You can analyse your site using a browser extension or online, more information: https://developers.google.com/speed/pagespeed/

= How to embed videos in WordPress? =
Easily insert the URL to your content (e.g. Youtube video) into a post or page. The URL must be on its own line and must not be hyperlinked. "WordPress will automatically turn [the URL] into a YouTube embed when the post is viewed." (Source: https://codex.wordpress.org/Embeds)
Inserting a Youtube `<iframe>` (instead of the plain URL) is deprecated and not supported by Lazy Load for Videos.

= How to disable lazy-loading of a single video? =
Append `&lazyload=0` to the video URL.

= How to add support for custom post types? =
UPDATE: Since plugin version 2.1.2, every custom post type is supported automatically. So you can ignore the following instructions. They will be removed with one of the next updates.

You can use a filter to add support for a custom post type (since version 2.0.4).
Replace 'post_type_label' with the name/label of your custom post type.

`/**
 * Set post types that shall support Lazy Load for Videos
 */
function lazyload_videos_set_post_types( $post_types ) {
	$post_types[] = 'post_type_label';
	return $post_types;
}
add_action( 'lazyload_videos_post_types', 'lazyload_videos_set_post_types' );`

= How to use a custom play button? =
For now, you can choose the "Youtube button image" from the play button drop-down list, then add the following custom CSS that includes a link to your custom CSS play button image:

```
.preview-youtube .lazy-load-youtube-div, .lazy-load-vimeo-div {
	background-image: url(INSERT-YOUR-URL-HERE.../images/play.png);
}
```

Feature versions might include an option to change the colour of your CSS-only buttons using a colour picker and might also include an option to directly upload the desired button image.

= How to lazy load playlists? =
Similar to a single video, insert the playlist URL in the following format:
`https://www.youtube.com/watch?v=dkfQFih23Ak&list=PLRQFBJ3mkjnxaPhAVOzjxxv_0yr8XE0Ja` (the other format - `https://www.youtube.com/playlist?list=...` - is not supported currently).
Note that playlists are not working when you're using the pre-/post-roll feature.

= Known bugs - this plugin may not work correctly when one of the following plugins is activated... =
* "YouTube" (https://wordpress.org/extend/plugins/youtube-embed-plus/)
* "Shortcode Embeds" extension within Jetpack, see answer above (https://jetpack.me/support/shortcode-embeds/)
* "BuddyPress" (https://wordpress.org/plugins/buddypress/)
* Thumbnails from Vimeo videos in "Privacy Mode" are not supported because Vimeo’s API doesn’t deliver thumbnails for those videos.
* [???]


== Changelog ==

= 2.9.0 =
* Change minimum browser support from IE10 to IE11
* Smarter code splitting: Now Webpack splits shared code into separate bundles so that developers don't need to manually maintain an extra Webpack entry. WordPress will load up three JS files if both Youtube and Vimeo are supported on your blog: lazyload-shared.js, lazyload-youtube.js, lazyload-vimeo.js, instead of one big lazyload-all.js
* Allow filtering Vimeo-specific CSS classes using "lazyload_preview_url_css_vimeo" filter
* Rename folder "assets" to "public"
* Rename filter lazyload_preview_url_a_class_youtube to lazyload_preview_url_class_youtube
* Use "wp_add_inline_script" (requires WordPress 4.5 or higher) instead of "wp_localize_script" and improve how scripts are being loaded
* Increase minimum WordPress version to 4.5
* The Youtube and Video configs are now stored in window.llvConfig instead of window.lazyload_video_settings
* Remove RTL-specific CSS used for admin panel because it wasn't properly maintained
* Delete oembed caches when uninstalling plugin, not just when deactivating it
* Delete transient oembed caches whenever oembed caches get deleted
* No longer run $.bindFirst
* Database: Rename value associated with standard thumbnail quality from "0" to "basic". This value is stored in the "lazyload_thumbnail_quality" post meta row
* Database: No longer add a "lazyload_thumbnail_quality" field to the DB if the value is "default"
* Database: Remove "lazyload_thumbnail_quality" post meta when uninstalling the plugin

= 2.8.7 =
* Fix: White arrow was overlaying the red Youtube play button
* A few tiny invisible improvements

= 2.8.6 =
* Fix: On some sites videos had large whitespace above and disappeared on play
* Fix: Video jumped for on hover by one pixel in some themes and in theme preview

= 2.8.5 =
* Undo 2.8.4

= 2.8.4 =
* Fix: Videos disappeared in some themes when the block editor is used

= 2.8.2 =
* Fix: Make Youtube start param without "s" work, e.g. "?t=17" (17 seconds)

= 2.8.1 =
* Fix: Show correct thumbnails if multiple Youtube videos are on the same page
* Speed up resizing of thumbnails on load

= 2.8.0 =
* Lazy load video preview images, not just the video 🎉
* Make "responsive mode" the default and only option

= 2.7.8 =
* Load thumbnails for domain-restricted Vimeos (and possibly some other Vimeo cases)
* Change language "es_MX" to "es_ES"

= 2.7.7 =
* Add Spanish translation. PR from @po5i on Github: https://github.com/kevinweber/lazy-load-for-videos/pull/34

= 2.7.6 =
* Remove "VideoObject" video attributes because some mandatory descriptors are missing

= 2.7.5 =
* Fix to allow autoplay Vimeo player on Chrome. PR from @po5i on Github: https://github.com/kevinweber/lazy-load-for-videos/pull/33
* Fix to make Vimeo URLs with query params in it work
* Fix to prevent PHP warning "Invalid argument supplied for foreach()"

= 2.7.4 =
* Fix issue where plugin didn't work for users who never updated the settings of this plugin

= 2.7.2 =
* Improve "Only load CSS/JS when needed" feature by scanning for embeds on pages with multiple posts (e.g. homepage, archive).
* Add filter: lazyload_videos_should_scripts_be_loaded
* No longer support "SCRIPT_DEBUG" variable for development

= 2.7.0 =
* ️️⚡ Performance: Independence from jQuery! The user-facing part of this plugin no longer requires jQuery.
* Privacy: Load all Youtube videos in privacy-enhanced mode using the https://www.youtube-nocookie.com URL
* Rewrite large parts of the JavaScript to make maintenance and open source contributions easier and less risky.
* Improve Ajax support, including support for Ajax Page Loader plugin. Thanks to @devattendant's [PR](https://github.com/kevinweber/lazy-load-for-videos/pull/19).
* Show a link to the video if the browser doesn't support JavaScript
* Remove option to display a credit link in the top right corner of each video. This further reduces CSS and JS file sizes. Please consider donating instead: https://www.kweber.com/donate/LazyLoadVideos/.
* Remove Youtube feature "Schema.org Markup"
* Remove Youtube feature "Hide Youtube logo"
* Remove Youtube feature "Player colour" (has been deprecated for a while)
* Remove Youtube feature "Relations" (Youtube no longer supports hiding related videos)
* Remove Youtube feature "Hide title/uploader" (no longer supported by Youtube)
* Reduce risk of conflicting class names with other plugins, see [issue](https://github.com/kevinweber/lazy-load-for-videos/issues/18).

= 2.6.0 =
* New feature: Hide Youtube logo from control bar by using Youtube's modest branding feature.
* Extend iframe code with allow attribute and values recommended by Youtube.

= 2.5.1 =
* Use https for thumbnails.
* Display background correctly if same Vimeo video is placed on the same page repeatedly.

= 2.5 =
* NOTE: The HTML and CSS for videos has been adjusted, mostly affecting the Vimeo embed. If you've added any custom CSS, be warned. Otherwise, no need to worry.
* NOTE 2: If video titles aren't showing as expected, click on the "Update Posts" button on the admin page for this plugin.
* A11y: Make it easier for screen readers to select both video types, Youtube and Vimeo.
* I18n: Make "Play video" text translatable.
* Fixed Vimeo title that didn't show up if background pattern option was selected.
* Reduced CSS and JS file sizes.

= 2.4 =
* New feature: Display a pattern instead of a thumbnail and thereby avoid requests to a video platform (until the user clicks play and requests become necessary).
* Automated CSS prefixing. Some prefixes are no longer added. (Supported browsers are defined in .browserslistrc file.)
* For developers: Update all dependencies and Webpack.

= 2.3 =
* New feature: Disable lazy-loading of a single video by appending `lazyload=0` to the video URL.
* Solved AMP error: "The attribute 'video-title' may not appear in tag 'a'.". NOTE: The change will only affect new and updated posts. Click on "Update Posts" in the admin panel to apply the change to all posts/pages.
* Big refactoring: The plugin's JavaScript and CSS is now organized in modules and the output files are generated using Webpack. This refactoring makes contributions possible/easier and allows development using modern, future-proof JavaScript.

= 2.2.3 =
* Don't show video link if preview image isn't loaded yet.
* Wait with displaying preview image until initial resize of video is done.
* Fallback if visitor has no JavaScript: Display video title and URL.
* Feature "Player color" for Youtube videos is now deprecated because Youtube doesn't support it for its HTML5 player.
* Reduced play button image file sizes.

= 2.2.2.2 =
* Prevent other themes/plugins (such as BBPress) from displaying Youtube videos "inline" because this causes videos not to be displayed.
* Set the default margin-bottom for iframes/embeds to "0".

= 2.2.2.1 =
* Now you can add a custom start time to each video URL. Use it like this: ...url.../watch?v=VIDEO_ID&t=XhYmZs or .../watch?v=VIDEO_ID&t=Z (replace X/Y/Z with hours/minutes/seconds). Thanks to @R33D3M33R's [PR](https://github.com/kevinweber/lazy-load-for-videos/pull/8).
* Removed "http" from URLs for https compatibility.

= 2.2.1.2 =
* Added Serbian translation by Ogi Djuraskovic.

= 2.2.1.1 =
* Fixed not working option to disable Vimeo.
* Removed callback functionality.
* Merged pull request from @R33D3M33R on Github (https://github.com/kevinweber/lazy-load-for-videos/pull/7): Load video at custom start time wasn't working properly. Background-image was always overriden, even if user has set a custom one.

= 2.2.1 =
* The "i" link is now optional. By default, no information link is displayed.

= 2.2.0.4 =
* Important: The wp_footer function in your theme is now required.
* Combined several JavaScript files into one. Thanks to @summatix's [PR](https://github.com/kevinweber/lazy-load-for-videos/pull/5).
* Added fallback by @summatix to load thumbnail with default quality when a high quality version is not available. Removed "Force maximum resolution" option because the fallback makes this feature unnecessary.
* Made plugin translatable.

= 2.2.0.3 =
* Fix: Updated URL definitions to improve js minify (merge request via Github by @sigginet)
* Changed the background colour of loading preview images from black to transparent. You can use the following custom CSS to use black instead: .preview-lazyload { background-color: #000 !important; }

= 2.2.0.2 =
* Fixed not working pre-roll and post-roll feature (values had not been stored).

= 2.2.0.1 =
* The pre-roll and post-roll ads feature is now available for free! Please consider an appropriate donation.
* Updated screenshots.

= 2.1.5.1 =
* Added callback function.

= 2.1.5 =
* Fix to make Vimeo working again.

= 2.1.4 =
* Improvement: MUCH ENHANCED performance when updating all posts. Fewer queries, faster execution. Should fix the "Allowed memory size exhausted" issue that appeared on large sites with many posts.
* Improvement: Support every kind of post type.
* Fix/new feature: By default, max resolution is only used when a singular post/page is displayed. Users can choose to also load high quality thumbnails on archives and other pages using a "force" checkbox.
* Fix: Replaced '<?=' with '<?php echo'.
* Improvement: Use not minified JavaScript files when SCRIPT_DEBUG is true (defined in wp-config.php).
* Added version number to scripts.

= 2.1.1 =
* Fix: Replaced incorrect 'INCOLL_TDM_TD' variable.
* Fixed not correct commented out variables.
* Fix: Replaced '<?=' with '<?php echo'.

= 2.1 =
* New feature: Add schema.org markup to your Youtube and Vimeo videos.
* New feature: Hide title/uploader. Don't display information like the video title and uploader when the video starts playing.
* New feature: Hide annotations (such as "subscribe to channel").
* Improved colour picker.
* Fix: Made 'Update Posts' working again.

= 2.0.7 =
* Added red play button to the list of play buttons.
* Added support for three more post types (any, home_slider, nectar_slider).
* Fix: Display videos on BuddyPress activity streams.
* Fix: Don't duplicate branding links (as seen on BuddyPress activity streams).
* Fix: Actually clear oembed cache when post is updated.

= 2.0.6 =
* Improvement: Reduced memory usage when all posts are updated.
* Thumbnail size "cover" is now default.
* Fixed a responsive video bug.

= 2.0.5 =
* Fix: Manually inserted links for Youtube playlists are working again.

= 2.0.4 =
* Fix: "Update Posts" now also works for pages (not only for posts).
* Added support for several often used post types (portfolio, news, article, articles, event, events, testimonial, testimonials, client, clients).

= 2.0.3 =
* Improvement: Automatically clear oembed cache when a post is updated.

= 2.0.2 =
* Fixed "unexpected T_PAAMAYIM_NEKUDOTAYIM".

= 2.0 =
* New feature: Youtube and Vimeo videos are responsive now! The video height automatically adjusts to its width
* New feature: Set default thumbnail quality. Choose between standard and maximum resolution. You can override the default setting on every post and page individually.
* Two new styles for the play button: "White Pulse" and "Black Pulse".
* Improvement: Use POST method to update all posts/pages.
* Added very basic RTL (right-to-left) language support.

= 1.6.2 =
* New feature (beta): Apply schema.org markup to videos
* Fix: The new CSS play buttons caused some errors that have been fixed with this update
* Fix: Use WordPress' built in function to delete oembed caches. Much better performance! Now, again, do update all posts that have an oembedded medium when user activates the plugin
* Fix: Actually remove the "i" link when the option to remove it is checked
* Fix to make the plugin ready for WordPress 4.0 and its new feature to display video previews in editor

= 1.6.1 =
* Wrapped videos into a div-container.

= 1.6 =
* New feature: Choose from three play buttons (CSS-only white, CSS-only black, Youtube button image)
* New premium feature: Convert Youtube videos into a playlist and automatically add your corporate video, product teaser or another video advertisement at the end of every Youtube video
* New premium feature: Remove branding
* New feature: With a colour picker the user can choose a colour of the video controls (Vimeo only)
* New feature: Support for Youtube and Vimeo URLs in tables created with plugin TablePress
* Improvement: User must not update articles anymore when he changes setting 'Display Youtube title'
* Fix/new feature: Users can activate an option to only load CSS/JS files on pages/posts when necessary. (It can happen that – when this option is activated – videos on pages do not lazy load although they should.)

= 1.5.2.1 =
* Fix: Do NOT update all posts that have an oembedded medium when user activates the plugin anymore

= 1.5.2 =
* New feature: When user -activates- or deactivates this plugin, all posts that have an oembedded medium will be updated once automatically
* New feature: Users can update all existing posts manually
* Improvement: Only load CSS/JS files on pages/posts when necessary

= 1.5 =
* New feature: Choose between two colours for Youtube player (dark or light)
* New feature: Support for widgets (Youtube only)
* New feature: Choose thumbnail size (standard or cover)
* New feature: Don’t display related videos at the end of your videos (Youtube only)
* New feature: Hide controls from Youtube player (Youtube only)
* New feature: Choose between two colours for Youtube player’s video progress bar to highlight the amount of the video that the viewer has already seen (red or white)

= 1.4.2 =
* Important bugfix: Plugin v1.4 has not worked for new and updated posts
* Bugfix: On some sites the plugin did not work as expected because a CSS height was not defined

= 1.4 =
* New feature: Display video titles on preview images (for Youtube and Vimeo)
* New feature: Admins can add Custom CSS via options panel

= 1.3 =
* New feature: Support for Vimeo videos!!
* New options panel for admins (now you can deactivate Lazy Load for Youtube/Vimeo in backend)
* SEO: The preview image's title attribute contains video's title

= 1.2.1 =
* Bugfix: 'Infinite Scroll' plugins are compatible with this plugin now

= 1.2 =
* Added jQuery.noConflict to avoid some bugs (See: https://api.jquery.com/jQuery.noConflict)
* This plugin now uses jQuery that comes with WordPress

= 1.1 =
* Plugin goes public.


== Upgrade Notice ==

= 2.2.3 =
* This plugin will automatically update all embed links in your database to apply the newest updates. This is the same procedure that can be manually triggered using the "Update Posts" button on the admin page.

= 2.2.2.2 =
* The default margin-bottom for iframes/embeds is set to "0". This might affect your blog's styling. So please check what happens if a user clicks on a Youtube video. You might have to override the margin-bottom value using CSS and "!important".

= 1.6.2 =
* Several important fixes. Please upgrade.

= 1.6.1 =
* Wrapped videos into a <div> container. Now you can change the player size with custom CSS, like so: .container-youtube, .container-vimeo { max-width: 50%; }

= 1.6 =
* This update implies some changes to the video styling. Please check and update the plugin settings.

= 1.2 =
* Plugin should now work on many more sites correctly.

= 1.1 =
* Plugin goes public.


== Screenshots ==

1. Preview image with video title and white play button.
2. Preview image and red play button.
3. Options panel for admins (v2.2).
4. Options panel for admins (v2.2).
5. Options panel for admins (v2.2).
