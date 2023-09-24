Lazy Load for Videos by [Kevin Weber](https://www.kweber.com)
====================

This plugin improves page load time and increases your Google PageSpeed Score. It works with oEmbed and replaces embedded Youtube and Vimeo videos with a clickable preview image.
By loading videos only when the user clicks on the preview image, no unnecessary JavaScript is loaded. Especially on sites with many embedded videos this will make your visitors happy. Additionally, all Youtube videos are loaded in a privacy-enhanced mode using the "<https://www.youtube-nocookie.com>" embed URL.

[Download link and more information on the developer's website](https://www.kweber.com/lazy-load-videos/)

## How to contribute?

This is open source. Everyone can contribute, including you! I'm looking forward to review and merge your contribution. Here are a few steps to help you get started:

1. [Install Yarn v2](https://yarnpkg.com/getting-started/install#install-corepack).
1. Fork this repository and clone the forked repository to your computer.
1. Navigate to the downloaded folder in your terminal.
1. Afterwards you can run `yarn watch` to automatically compile all JavaScript and SCSS changes whenever you save a file.
1. Make sure that `define('SCRIPT_DEBUG', true);` is set in your wp-config.php so that non-chached scripts (without `?ver=2.16.5` in the URL) are loaded.
1. Ideally, write tests related to your changes. Make sure that all test cases are succeeding (run: `yarn test`).
1. When you're done, run `yarn production`.
1. Create a [pull request](https://help.github.com/articles/creating-a-pull-request/).

## Integration with other themes and plugins

### Filter: `lazyload_videos_should_scripts_be_loaded`

You can override when the JS and CSS of this plugin should be loaded. If you always return `true` as shown in the example below, the scripts will always be loaded, no matter if the page has a video embed or not.

```
function custom_theme_plugin__should_scripts_be_loaded($value) {
 // return $value;
 return true; // <- Always load scripts, no matter what page you're on
}
add_filter( 'lazyload_videos_should_scripts_be_loaded', 'custom_theme_plugin__should_scripts_be_loaded');
```

### Filter: `lazyload_videos_post_types`

The default set of post types where videos should be lazy-loaded comes from [get_post_types()](https://codex.wordpress.org/Function_Reference/get_post_types). Use this filter to extend/override the default.
