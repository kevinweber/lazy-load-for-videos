<?php
/*
 * Plugin Name: Lazy Load for Videos
 * Plugin URI: http://kevinw.de/lazy-load-videos/
 * Description: Lazy Load for Videos speeds up your site by replacing embedded Youtube and Vimeo videos with a clickable preview image. Visitors simply click on the image to play the video.
 * Author: Kevin Weber
 * Version: 2.0.5
 * Author URI: http://kevinw.de/
 * License: GPL v3
 * Text Domain: lazy-load-videos
*/

/*
	Copyright (C) 2014 Kevin Weber

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

define( 'LL_VERSION', '2.0.5' );

if ( !defined( 'LL_FILE' ) ) {
	define( 'LL_FILE', __FILE__ );
}

if ( !defined( 'LL_ESSENTIAL' ) ) {
	define( 'LL_ESSENTIAL', true );	// Should be false if this is the 'Premium' version
}

if ( !defined( 'LL_PATH' ) )
	define( 'LL_PATH', plugin_dir_path( __FILE__ ) );

require_once( LL_PATH . 'admin/inc/define.php' );
require_once( LL_PATH . 'admin/class-register.php' );
require_once( LL_PATH . 'inc/class-general.php' );

function lazyload_init_plugins_loaded() {
	require_once( LL_PATH . 'admin/class-admin-options.php' );
	require_once( LL_PATH . 'frontend/class-frontend.php' );
}
add_action( 'plugins_loaded', 'lazyload_init_plugins_loaded', 15 );



function admin_init() {
	if ( LL_ESSENTIAL ) {
		include_once( LL_PATH . 'admin/inc/class-no-premium.php'); 
	}
	require_once( LL_PATH . 'admin/class-meta.php' );
}
function frontend_init() {
	// Feature: Support for Widgets (Youtube only)
	if ( (get_option('lly_opt_support_for_widgets') == true) ) {
		require_once( LL_PATH . 'frontend/inc/support_for_widgets.php');
	}
	// Feature: Support for Plugin "TablePress"
	if ( (get_option('ll_opt_support_for_tablepress') == true) ) {
		require_once( LL_PATH . 'frontend/inc/support_for_tablepress.php');
	}
}

if ( is_admin() ) {
	add_action( 'plugins_loaded', 'admin_init', 16 );
}
else {
	add_action( 'plugins_loaded', 'frontend_init', 16 );
}


/***** Plugin by Kevin Weber || kevinw.de *****/
?>