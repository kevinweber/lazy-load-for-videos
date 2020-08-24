<?php
class Lazy_Load_For_Videos_Frontend {

	function __construct() {
		$should_load_scripts = apply_filters( 'lazyload_videos_should_scripts_be_loaded', $this->should_scripts_be_loaded());

		if ($should_load_scripts) {
			require( LL_PATH . 'src/php/static-styles.php' );
			Lazy_Load_For_Videos_Styles::enqueue();
			$this->initScripts();
		}
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

	function initScripts() {
		$isYoutubeEnabled = get_option('lly_opt') !== '1';
		$isVimeoEnabled = get_option('llv_opt') !== '1';

		if ($isYoutubeEnabled || $isVimeoEnabled) {
			wp_enqueue_script( 'lazyload-video-js', LL_URL . 'public/js/lazyload-shared.js', null, SCRIPT_DEBUG ? null : LL_VERSION, true );
		}

		if ($isYoutubeEnabled) {
			require( LL_PATH . 'src/php/static-youtube.php' );
			Lazy_Load_For_Videos_Youtube::enqueue();	
		}
		
		if ($isVimeoEnabled) {
			require( LL_PATH . 'src/php/static-vimeo.php' );
			Lazy_Load_For_Videos_Vimeo::enqueue();	
		}
	}
}

// Fires after enqueuing block assets for both editor and front-end.
add_action( 'wp_enqueue_scripts', function() {
	new Lazy_Load_For_Videos_Frontend();
} );
