=== Plugin Name ===
Contributors: kevinweber
Donate link: http://kevinw.de/donate/LazyLoadVideos/
Tags: youtube, vimeo, performance, admin, plugin, content, video, page, jquery, mobile
Requires at least: 3.0
Tested up to: 3.8
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Speed up your site by replacing embedded Youtube and Vimeo videos with a clickable preview image. Visitors simply click on the image to play the video.

== Description ==

This plugin improves page load times and increases your Google PageSpeed Score. It replaces embedded Youtube and Vimeo videos with a clickable preview image.
By loading the videos only when the user clicks on the preview image – using jQuery –, no unnecessary JavaScript is loaded. Especially on sites with many embedded videos this will make your visitors happy.

Future features:
* Choose between several "Play" buttons
* Upload your own "Play" button
* Choose between several colours for your player
* (Optional) video preview image for feeds
* What you suggest


== Installation ==

Easily upload Lazy Load for Videos into you plugin directory (/wp-content/plugins/) and activate the plugin through the 'Plugins' menu in WordPress.

You may have to clean the website's and browser's cache.

If you don't see a preview image instead of the Youtube video, open the post editor and update/save the post again. This should help.


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

= Solved: The plugin isn't working with Jetpack... =
"Jetpack" by WordPress.com offers some useful extensions. Only one of them makes Lazy Load for Videos break – the "Shortcode Embeds" extension. So simply disable the extension. (In order to see a "Deactivate" button for "Shortcode Embeds" on the Jetpack's extension overview, you must click on "Learn More".)

= Known bugs - this plugin may not work correctly when one of the following plugins is activated... =
* "YouTube" (http://wordpress.org/extend/plugins/youtube-embed-plus/)
* "Shortcode Embeds" extension within Jetpack, see answer above (http://jetpack.me/support/shortcode-embeds/)
* [???]


== Changelog ==

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

= 1.2 =
* Plugin should now work on many more sites correctly.

= 1.1 =
* Plugin goes public.


== Screenshots ==

1. Preview image and play button are displayed.
2. Options panel for admins (since version 1.3).