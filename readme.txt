=== Lazy Load for Videos ===
Contributors: kevinweber
Donate link: http://kevinw.de/donate/LazyLoadVideos/
Tags: youtube, vimeo, performance, admin, plugin, content, video, page, jquery, mobile
Requires at least: 3.0
Tested up to: 4.0
Stable tag: 2.0.5
License: GPL v3
License URI: http://www.gnu.org/copyleft/gpl.html

Speed up your site by replacing embedded Youtube and Vimeo videos with a clickable preview image. Visitors simply click on the image to play the video.

== Description ==

This plugin improves page load times and increases your Google PageSpeed Score. It replaces embedded Youtube and Vimeo videos with a clickable preview image.
By loading the videos only when the user clicks on the preview image – using jQuery –, no unnecessary JavaScript is loaded. Especially on sites with many embedded videos this will make your visitors happy.

Demo on the developer’s website: [kevinw.de/lazy-load-videos/](http://kevinw.de/lazy-load-videos/)

= Some additional features: =
* Display video titles on preview images
* Convert all videos into a playlist and automatically add your corporate video, product teaser or another video advertisement at the end of every video. (Great for branding and video ads!)
* Pre-roll and post-roll advertisements
* Add Custom CSS via plugin’s options panel
* Choose custom colour for your Vimeo player
* Hide controls from Youtube player
* Support for videos in text widget (Youtube only)
* Choose thumbnail size (standard or cover)
* Choose from several "Play" buttons
* Choose between two colours for Youtube player (dark or light)
* Choose between two colours for Youtube player’s video progress bar to highlight the amount of the video that the viewer has already seen (red or white)
* Don’t display related videos at the end of your videos

= Future features: =
* (Better) support with specific plugins
* Support for new/other formats, like SoundCloud, SlideShare and Spotify 
* Create a custom "Play" button with a colour picker
* More "Play" button styles
* Video preview image for feeds
* Track how often the videos have been loaded with Google Analytics
* What you suggest


== Installation ==

Easily upload Lazy Load for Videos into you plugin directory (/wp-content/plugins/) and activate the plugin through the 'Plugins' menu in WordPress.

When you had already published videos before you have activated Lazy Load for Videos, update all posts by clicking the "Update Posts" button below the "Save Changes" button.

You may have to clean the website's and browser's cache.

If you don't see a preview image instead of the Youtube/Vimeo video, open the post editor and update/save the post again or even update all posts using the above mentioned "Update Posts" button.


== Frequently Asked Questions ==

Search for keywords using "STRG + F" keyboard shortcut (Mac: "CMD + F").

= Where can I see this plugin in use? =
For example, on http://kevinw.de/greenbird/.

= Which video platforms are supported? =
Videos from the biggest video platform, Youtube (http://youtube.com), and Vimeo (http://vimeo.com) are supported.

= Does the Lazy Load for Videos plugin work when there is more than one video on the same page? =
Yes. The plugin works on single posts and pages as well as on archive pages with several posts and videos.

= Should I use the Lazy Load for Videos plugin? =
Yes!

= Why? =
* If you want to speed your site up, this plugin is for you. Especially on sites with many embedded videos this plugin is helpful. There are multiple reasons why you should make your site faster, see next question.
* The plugin is really lightweight and does neither blow your performance nor your database up.
* By loading the videos only when the user clicks on the preview image, no unnecessary JavaScript is loaded. You may know this issue: (Defer) Parsing of JavaScript. "In order to load a page, the browser must parse the contents of all <script> tags, which adds additional time to the page load. By minimizing the amount of JavaScript needed to render the page, and deferring parsing of unneeded JavaScript until it needs to be executed, you can reduce the initial load time of your page." (Source: https://developers.google.com/speed/docs/best-practices/mobile#DeferParsingJS)
* Video preview and play button are scalable and optimized for mobile devices.
* Finally, Lazy Load for Videos is really easy to use. Simply upload and activate the plugin.

= Why are a faster website and a higher Google PageSpeed Score great? =
* Search engines, especially Google, love speedy sites! So you will be higher ranked in search results.
* Consequently, you get more visitors.
* Fast sites lead to higher visitor engagement and returning visitors.
* Moreover, faster sites increase conversions.

= How can I calculate my Google PageSpeed Score? =
PageSpeed Insights is a service by Google to help you optimize your site's performance. You can analyse your site using a browser extension or online, more information: https://developers.google.com/speed/pagespeed/

= How to embed videos in WordPress? =
Easily post the URL to your content (e.g. Youtube video) into a post or page. The URL must be on its own line and must not be hyperlinked. "WordPress will automatically turn [the URL] into a YouTube embed when the post is viewed." (Source: http://codex.wordpress.org/Embeds)

= How to add support for custom post types? =
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

= Solved: The plugin isn't working with Jetpack... =
"Jetpack" by WordPress.com offers some useful extensions. Only one of them makes Lazy Load for Videos break – the "Shortcode Embeds" extension. So simply disable the extension. (In order to see a "Deactivate" button for "Shortcode Embeds" on the Jetpack's extension overview, you must click on "Learn More".)

= Known bugs - this plugin may not work correctly when one of the following plugins is activated... =
* "YouTube" (http://wordpress.org/extend/plugins/youtube-embed-plus/)
* "Shortcode Embeds" extension within Jetpack, see answer above (http://jetpack.me/support/shortcode-embeds/)
* "BuddyPress" (http://wordpress.org/plugins/buddypress/)
* Thumbnails from Vimeo videos in "Privacy Mode" are not supported because Vimeo’s API doesn’t deliver thumbnails for those videos.
* [???]


== Changelog ==

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
* Wrapped videos into a <div> container.

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
* Added jQuery.noConflict to avoid some bugs (See: http://api.jquery.com/jQuery.noConflict)
* This plugin now uses jQuery that comes with WordPress

= 1.1 =
* Plugin goes public.


== Upgrade Notice ==

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

1. Preview image and play button are displayed.
2. Options panel for admins (version 1.5).