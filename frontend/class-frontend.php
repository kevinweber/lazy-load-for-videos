<?php
require_once( LL_PATH . 'frontend/class-frontend-init-styles.php' );
require_once( LL_PATH . 'frontend/class-frontend-init-scripts.php' );

/**
 * @package Frontend
 */
class Lazy_Load_For_Videos_Frontend {
	function registerAll() {
		// wp_deregister_script('jquery'); // <= For development: Deregister jQuery to ensure this plugin works without jQuery
		wp_register_style( 'lazyload-video-css', LL_URL . 'assets/css/lazyload-all.css' );
		wp_register_script( 'lazyload-video-js', LL_URL . 'assets/js/lazyload-all.js', null, LL_VERSION, true );
		wp_register_script( 'lazyload-vimeo-js', LL_URL . 'assets/js/lazyload-vimeo.js', null, LL_VERSION, true );
		wp_register_script( 'lazyload-youtube-js', LL_URL . 'assets/js/lazyload-youtube.js', null, LL_VERSION, true );
	}

	/**
	 * Don't load scripts in specific circumstances
	 */
	function should_scripts_be_loaded() {
		if (
			( get_option('ll_opt_load_scripts') != '1' ) ||	// Option "Only load CSS/JS when needed" is NOT checked
			( get_option('lly_opt_support_for_widgets') == true ) // Always load scripts if widgets need lazy load support (Youtube only)
		) {
			return true;
		}
		
		global $lazyload_videos_general;
		if (is_singular()) {
			$post_id = absint(get_the_ID());
			return $lazyload_videos_general->has_post_or_page_embed($post_id);
		}
		
		// For pages with multiple posts (e.g. homepage and archives),
		// iterate over all posts to see if any of them includes an embed.
		global $posts;
		if (is_array($posts)) {
			foreach($posts as $post) {
				$has_post_embed = $lazyload_videos_general->has_post_or_page_embed($post->ID);
				if ($has_post_embed) return true;
			};
		}

		return false;
	}
}

function lazyload_videos_frontend() {
	$frontend = new Lazy_Load_For_Videos_Frontend();
	$should_load_scripts = apply_filters( 'lazyload_videos_should_scripts_be_loaded', $frontend->should_scripts_be_loaded());

	if ($should_load_scripts) {
		$frontend->registerAll();

		$styles = new Lazy_Load_For_Videos_Init_Styles();
		$styles->init();

		$scripts = new Lazy_Load_For_Videos_Init_Scripts();
		$scripts->init();
	}
}

add_action( 'wp_enqueue_scripts', 'lazyload_videos_frontend' );
