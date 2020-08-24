<?php
class Lazy_Load_For_Videos_Youtube {
	
	static function enqueue() {
		wp_enqueue_script( 'lazyload-youtube-js', LL_URL . 'public/js/lazyload-youtube.js', null, SCRIPT_DEBUG ? null : LL_VERSION, true );
		wp_add_inline_script(
			'lazyload-youtube-js',
			Lazy_Load_For_Videos_Youtube::get_inline_script(),
			'before'
		);
	}

	static function get_inline_script() {
		return 'window.llvConfig=window.llvConfig||{};window.llvConfig.youtube=' . json_encode(Lazy_Load_For_Videos_Youtube::get_config()) . ';';
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
			'thumbnailquality' => Lazy_Load_For_Videos_Youtube::thumbnailquality(),
			'preroll'          => get_option( 'lly_opt_player_preroll', '' ),
			'postroll'         => get_option( 'lly_opt_player_postroll', '' ),
			'loadthumbnail'	 => Lazy_Load_For_Videos_Youtube::should_load_thumbnail(),
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
		global $lazyload_videos_general;
		return $lazyload_videos_general->get_thumbnail_quality();
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
