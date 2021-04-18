<?php
class KW_LLV_Youtube {
	// Don't change those strings since exactly those strings are needed by the Youtube JavaScript file
	static $js_thumbnailquality_default = '0';
	static $js_thumbnailquality_sddefault = 'sddefault';
	static $js_thumbnailquality_maxresdefault = 'maxresdefault';
	
	
	static function enqueue() {
		wp_enqueue_script( 'lazyload-youtube-js', LL_URL . 'public/js/lazyload-youtube.js', null, SCRIPT_DEBUG ? null : LL_VERSION, true );
		wp_add_inline_script(
			'lazyload-youtube-js',
			KW_LLV_Youtube::get_inline_script(),
			'before'
		);
	}

	static function get_inline_script() {
		return 'window.llvConfig=window.llvConfig||{};window.llvConfig.youtube=' . json_encode(KW_LLV_Youtube::get_config()) . ';';
	}

	/**
	 * Lazy Load Youtube Videos (Load youtube script and video after clicking on the preview image)
	 * Thanks to »Lazy loading of youtube videos by MS-potilas 2012« (see http://yabtb.blogspot.com/2012/02/youtube-videos-lazy-load-improved-style.html)
	 */
	static function get_config() {
		return apply_filters( 'lly_change_options', array(
			'colour'           => get_option( 'lly_opt_player_colour_progress', 'red' ),
			'buttonstyle'      => get_option( 'll_opt_button_style', '' ),
			'controls'         => ! ( get_option( 'lly_opt_player_controls' ) == '1' ),
			'loadpolicy'       => ! ( get_option( 'lly_opt_player_loadpolicy' ) == '1' ),
			'thumbnailquality' => KW_LLV_Youtube::thumbnailquality(),
			'preroll'          => get_option( 'lly_opt_player_preroll', '' ),
			'postroll'         => get_option( 'lly_opt_player_postroll', '' ),
			'overlaytext'      => trim(get_option( 'lly_opt_overlay_text', '')),
			'loadthumbnail'	   => KW_LLV_Youtube::should_load_thumbnail(),
			'cookies'	       => get_option( 'lly_opt_cookies' ) == '1',
			'callback'         => '<!--YOUTUBE_CALLBACK-->'
		) );
	}

	static function should_load_thumbnail() {
		$thumbnail = get_option('ll_opt_thumbnail_size');
		return $thumbnail == '' || $thumbnail == 'standard' || $thumbnail == 'cover';
	}

 	/**
 	 * Test which thumbnail quality should be used
 	 */
 	static function thumbnailquality() {
		global $post;

		if (!isset($post->ID)) {
			$id = null;
		}
		else {
			$id = $post->ID;
		}

		// When the individual status for a page/post is '0', all the other settings don't matter.
		$post_thumbnail_quality = get_post_meta( $id, 'lazyload_thumbnail_quality', true );

		if (
			$post_thumbnail_quality === 'max'
			|| ( empty($post_thumbnail_quality) && ( get_option('ll_opt_thumbnail_quality') === 'max' ) )
			// "lly_opt_thumbnail_quality" is deprecated and can no longer be set. It's here for backward compatibility.
			|| ( empty($post_thumbnail_quality) && ( get_option('lly_opt_thumbnail_quality') === 'max' ) )
			// Need to check for "default" value for backward compatibility because this plugin used to store "default" in the DB,
			// and now we're not storing any value in the default case anymore.
			// See: https://github.com/kevinweber/lazy-load-for-videos/pull/48/files#diff-a7050d7d07c23aab4907f6e32ef248cdR101
			|| ( $post_thumbnail_quality === 'default' && ( get_option('lly_opt_thumbnail_quality') === 'max' ) )
			) {
			return KW_LLV_Youtube::$js_thumbnailquality_maxresdefault;
		}

		if (
			$post_thumbnail_quality === 'medium'
			|| ( empty($post_thumbnail_quality) && ( get_option('ll_opt_thumbnail_quality') === 'medium' ) )
			// "lly_opt_thumbnail_quality" is deprecated and can no longer be set. It's here for backward compatibility.
			|| ( empty($post_thumbnail_quality) && ( get_option('lly_opt_thumbnail_quality') === 'medium' ) )
			// Need to check for "default" value for backward compatibility because this plugin used to store "default" in the DB,
			// and now we're not storing any value in the default case anymore.
			// See: https://github.com/kevinweber/lazy-load-for-videos/pull/48/files#diff-a7050d7d07c23aab4907f6e32ef248cdR101
			|| ( $post_thumbnail_quality === 'default' && ( get_option('lly_opt_thumbnail_quality') === 'medium' ) )
			) {
			return KW_LLV_Youtube::$js_thumbnailquality_sddefault;
		}

		return KW_LLV_Youtube::$js_thumbnailquality_default;
 	}

 	/**
 	 * Callback
 	 * expects JavaScript code as string
 	 */
 	static function callback() {
 		$js = apply_filters( 'lly_set_callback', '' );
 		return $js;
	 }

}
