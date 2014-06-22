<?php
/*
 * Plugin Name: Lazy Load for Videos
 * Plugin URI: http://kevinw.de/lazyloadvideos.php
 * Description: Lazy Load for Videos speeds up your site by replacing embedded Youtube and Vimeo videos with a clickable preview image. Visitors simply click on the image to play the video.
 * Author: Kevin Weber
 * Version: 1.5.1
 * Author URI: http://kevinw.de/
 * License: GPL v3
 * Text Domain: lazy-load-videos
*/

define( 'LL_VERSION', '1.5.1' );

if ( !defined( 'LL_FILE' ) ) {
	define( 'LL_FILE', __FILE__ );
}

if ( !defined( 'LL_PATH' ) )
	define( 'LL_PATH', plugin_dir_path( __FILE__ ) );

require_once( LL_PATH . 'admin/inc/signup_define.php' );
require_once( LL_PATH . 'admin/class-register.php' );
require_once( LL_PATH . 'inc/class-general.php' );

function lazyload_init_plugins_loaded() {
	require_once( LL_PATH . 'admin/class-admin-options.php' );
	require_once( LL_PATH . 'frontend/class-frontend.php' );
}

// Feature: Support for Widgets (Youtube only)
if ( (get_option('lly_opt_support_for_widgets') == true) && !is_admin() ) {
	require_once( LL_PATH . 'frontend/inc/support_for_widgets.php');
}

add_action( 'plugins_loaded', 'lazyload_init_plugins_loaded', 15 );

/***** Plugin by Kevin Weber || kevinw.de *****/
?>