<?php

class KW_LLV_Frontend {
	private $loadYoutube;
	private $loadVimeo;
	
	function __construct() {
		$this->loadYoutube = get_option('lly_opt') !== '1';
		$this->loadVimeo = get_option('llv_opt') !== '1';

		// Both video types are disabled? Don't do anything.
		if (!$this->loadYoutube && !$this->loadVimeo) return;

		$shouldOnlyLoadScriptsIfNeeded = apply_filters( 'lazyload_videos_should_scripts_be_loaded', $this->should_only_load_scripts_if_needed());
		if ($shouldOnlyLoadScriptsIfNeeded) {
			if (is_singular()) {
				$post_id = absint(get_the_ID());
				$this->loadYoutube = $this->loadYoutube && $this->has_post_embed_with_value($post_id, "%youtube%");
				$this->loadVimeo = $this->loadVimeo && $this->has_post_embed_with_value($post_id, "%vimeo%");
			} else {
				$this->have_posts_video_embeds();
			}

			// Both video types are STILL disabled? Don't do anything.
			if (!$this->loadYoutube && !$this->loadVimeo) return;
		}

		// Load CSS
		require_once( LL_PATH . 'src/php/static-styles.php' );
		KW_LLV_Styles::enqueue();

		// Load shared JS
		wp_enqueue_script( 'lazyload-video-js', LL_URL . 'public/js/lazyload-shared.js', null, SCRIPT_DEBUG ? null : LL_VERSION, true );

		// Load Youtube-specific JS
		if ($this->loadYoutube) {
			require_once( LL_PATH . 'src/php/static-youtube.php' );
			KW_LLV_Youtube::enqueue();	
		}
		
		// Load Vimeo-specific JS
		if ($this->loadVimeo) {
			require_once( LL_PATH . 'src/php/static-vimeo.php' );
			KW_LLV_Vimeo::enqueue();	
		}
	}

	function should_only_load_scripts_if_needed() {
		if (
			( get_option('ll_opt_load_scripts') != '1' ) ||	// Option "Only load CSS/JS when needed" is NOT checked
			( get_option('lly_opt_support_for_widgets') == true ) // Always load scripts if widgets need lazy load support (Youtube only)
		) {
			return false;
		}

		return true;
	}

	/**
	 * Checks if a post with oembed meta data has a meta value with a specific substring.
	 * Example for $meta_value input:
	 * "%youtube%" means "'youtube' is anywhere within meta_value".
	 */
	function has_post_embed_with_value($post_id, $meta_value) {
		global $wpdb;
		$query_result = $wpdb->query(
			$wpdb->prepare( 
				"SELECT meta_value FROM `".$wpdb->postmeta."`
					WHERE post_id = %d
					AND (
						`meta_key` LIKE \"_oembed%%\"
						OR `meta_key` LIKE \"oembed_%%\"
					)
					AND `meta_value` LIKE %s
				",
				$post_id,
				$meta_value
			)
		);
		return !empty($query_result);
	}

	function have_posts_video_embeds() {
		$loadYoutube = false;
		$loadVimeo = false;

		// For pages with multiple posts (e.g. homepage and archives),
		// iterate over all posts to see if they include an embed.
		global $posts;
		if (is_array($posts)) {
			foreach($posts as $post) {
				if ($loadYoutube && $loadVimeo) {
					break;
				}

				if (!$loadYoutube && $this->loadYoutube) {
					$loadYoutube = $this->has_post_embed_with_value($post->ID, "%youtube%");
				}

				if (!$loadVimeo && $this->loadVimeo) {
					$loadVimeo = $this->has_post_embed_with_value($post->ID, "%vimeo%");
				}
			};
		}

		$this->loadYoutube = $loadYoutube;
		$this->loadVimeo = $loadVimeo;
	}
}

// Fires after enqueuing block assets for both editor and front-end.
add_action( 'wp_enqueue_scripts', function() {
	new KW_LLV_Frontend();
} );
