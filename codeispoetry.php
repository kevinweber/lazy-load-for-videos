<?php
/*
 * Plugin Name: Lazy Load for Videos
 * Plugin URI: https://www.kweber.com/lazy-load-videos/
 * Description: Lazy Load for Videos speeds up your site by replacing embedded Youtube and Vimeo videos with a clickable preview image. Visitors simply click on the image to play the video.
 * Author: Kevin Weber
 * Version: 2.9.0
 * Author URI: https://www.kweber.com/
 * License: GPL v3
 * Text Domain: lazy-load-for-videos
 * Domain Path: /languages/
*/

/*
	Copyright (C) 2020 Kevin Weber

	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

if ( !defined( 'LL_OPTION_KEY' ) ) {
	define( 'LL_OPTION_KEY', 'lazyloadvideos' );
}

if (!defined('LL_VERSION'))
    define('LL_VERSION', '2.9.0');
if (!defined('LL_VERSION_KEY'))
    define('LL_VERSION_KEY', LL_OPTION_KEY.'_version');

if ( !defined( 'LL_FILE' ) )
	define( 'LL_FILE', __FILE__ );

if ( !defined( 'LL_ESSENTIAL' ) )
	define( 'LL_ESSENTIAL', true );	// Should be false if this is the 'Premium' version

if ( !defined( 'LL_TD' ) )
	define( 'LL_TD', 'lazy-load-for-videos' ); // = text domain (used for translations)

if ( !defined( 'LL_PATH' ) )
	define( 'LL_PATH', plugin_dir_path( __FILE__ ) );

if ( !defined( 'LL_URL' ) )
	define( 'LL_URL', plugin_dir_url( __FILE__ ) );


require_once( LL_PATH . 'src/php/inc/define.php' );
require_once( LL_PATH . 'src/php/class-register.php' );
require_once( LL_PATH . 'src/php/class-general.php' );


/**
 * Load plugin textdomain.
 * @since 2.2.0.4
 */
function lazyload_load_textdomain() {
  load_plugin_textdomain( LL_TD, false, dirname( plugin_basename( LL_FILE ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'lazyload_load_textdomain' );

function lazyload_videos_init_plugins_loaded() {
	require_once( LL_PATH . 'src/php/class-admin-options.php' );
	require_once( LL_PATH . 'src/php/class-frontend.php' );
	require_once( LL_PATH . 'src/php/class-editor.php' );
}

add_action( 'plugins_loaded', 'lazyload_videos_init_plugins_loaded', 15 );



function lazyload_videos_admin_init() {
	require_once( LL_PATH . 'src/php/class-meta.php' );
}

function lazyload_videos_frontend_init() {
	// Feature: Support for Widgets (Youtube only)
	if ( (get_option('lly_opt_support_for_widgets') == true) ) {
		require_once( LL_PATH . 'src/php/inc/support_for_widgets.php');
	}

	// Feature: Support for Plugin "TablePress"
	if ( (get_option('ll_opt_support_for_tablepress') == true) ) {
		require_once( LL_PATH . 'src/php/inc/support_for_tablepress.php');
	}
}

if ( is_admin() ) {
	add_action( 'plugins_loaded', 'lazyload_videos_admin_init', 16 );
} else {
	add_action( 'plugins_loaded', 'lazyload_videos_frontend_init', 16 );
}


function lazyload_theme_check() {
	include_once( LL_PATH . 'src/php/class-theme-check.php');
	$lazyload_theme_check = new Lazy_Load_For_Videos_Theme_Check();
	$lazyload_theme_check->theme_check_init('lazyload.php');
}
add_action( 'init', 'lazyload_theme_check' );

/***** Plugin by Kevin Weber || www.kweber.com *****/
?>
