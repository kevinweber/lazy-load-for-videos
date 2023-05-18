<?php
class KW_LLV_Vimeo {

	static function enqueue() {
		wp_enqueue_script( 'lazyload-vimeo-js', LL_URL . 'public/js/lazyload-vimeo.js', null, SCRIPT_DEBUG ? null : LL_VERSION, true );
		wp_add_inline_script(
			'lazyload-vimeo-js',
			KW_LLV_Vimeo::get_inline_script(),
			'before'
		);
	}

	static function get_inline_script() {
		return 'window.llvConfig=window.llvConfig||{};window.llvConfig.vimeo=' . json_encode(KW_LLV_Vimeo::get_config()) . ';';
	}

	/**
	 * Lazy Load VIMEO Videos (Load vimeo script and video after clicking on the preview image)
	 * Lazy Load for Vimeo works with URLs that look like: [Any Path]/[Video ID]
	 * Examples:
	 * http://vimeo.com/channels/staffpicks/48851874
	 * http://vimeo.com/48851874
	 * http://vimeo.com/48851874/
	 */
	static function get_config() {
		return apply_filters( 'llv_change_options', array(
			'buttonstyle'  => get_option( 'll_opt_button_style', '' ),
			'playercolour' => get_option( 'llv_opt_player_colour', '' ),
			'preroll'      => get_option( 'llv_opt_player_preroll', ''),
			'postroll'     => get_option( 'llv_opt_player_postroll', '' ),
			'show_title'   => get_option( 'llv_opt_title', false ) == true,
			'overlaytext' => trim(get_option( 'llv_opt_overlay_text', '')),
			'loadthumbnail'	 => KW_LLV_Vimeo::should_load_thumbnail(),
			'thumbnailquality' => KW_LLV_Vimeo::thumbnailquality(),
			'cookies'	       => get_option( 'llv_opt_cookies' ) == '1',
			'callback'     => '<!--VIMEO_CALLBACK-->'
		) );
	}

	static function should_load_thumbnail() {
		$thumbnail = get_option('ll_opt_thumbnail_size');
		return $thumbnail == '' || $thumbnail == 'standard' || $thumbnail == 'cover';
	}

	static function thumbnailquality() {
		global $post;

		if (!isset($post->ID)) {
			$id = null;
		}
		else {
			$id = $post->ID;
		}

		// Check and prioritize post-specific setting
		$post_thumbnail_quality = get_post_meta( $id, 'lazyload_thumbnail_quality', true );
		// Otherwise use general setting
		return empty($post_thumbnail_quality) ? get_option('ll_opt_thumbnail_quality') : $post_thumbnail_quality;
	}

	/**
 	 * Callback
 	 * expects JavaScript code as string
 	 */
	static function callback() {
		$js = apply_filters( 'llv_set_callback', '' );
		return $js;
	}
}
