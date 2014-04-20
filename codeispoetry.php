<?php
/*
 * Plugin Name: Lazy Load for Videos
 * Plugin URI: http://kevinw.de/lazyloadvideos.php
 * Description: Lazy Load for Videos speeds up your site by replacing embedded Youtube and Vimeo videos with a clickable preview image. Visitors simply click on the image to play the video.
 * Author: Kevin Weber
 * Version: 1.4.2
 * Author URI: http://kevinw.de/
 * License: GPL2+
 * Text Domain: lazy-load-videos
*/

if ( !defined( 'LL_PATH' ) )
	define( 'LL_PATH', plugin_dir_path( __FILE__ ) );

function lazyload_admin_init() {
	require_once( LL_PATH . 'admin/class-admin.php' );
}

function lazyload_frontend_init() {
	require_once( LL_PATH . 'frontend/class-frontend.php' );
	require_once( LL_PATH . 'frontend/class-youtube.php' );
	require_once( LL_PATH . 'frontend/class-vimeo.php' );
}

add_action( 'plugins_loaded', 'lazyload_admin_init', 15 );
add_action( 'plugins_loaded', 'lazyload_frontend_init', 15 );

/***** Plugin by Kevin Weber || kevinw.de *****/
?>