<?php
/**
 * @package Lazyload Youtube
 */
class Lazyload_Videos_Youtube {

	public function init() {
		if ( defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ) {
			wp_enqueue_script( 'lazyload_youtube_js', LL_URL . 'assets/js/lazyload-youtube.js', array( 'jquery' ), LL_VERSION, true );
		} else if ( get_option('lly_opt') !== '1' ) {
      wp_enqueue_script( 'lazyload-video-js', LL_URL . 'assets/js/lazyload-youtube.js', array( 'jquery' ), LL_VERSION, true );
    }
	}

	/**
	 * Lazy Load Youtube Videos (Load youtube script and video after clicking on the preview image)
	 * Thanks to »Lazy loading of youtube videos by MS-potilas 2012« (see http://yabtb.blogspot.com/2012/02/youtube-videos-lazy-load-improved-style.html)
	 */
	function get_js_settings() {
		return apply_filters( 'lly_change_options', array(
			'theme'            => get_option( 'lly_opt_player_colour', 'dark' ),
			'colour'           => get_option( 'lly_opt_player_colour_progress', 'red' ),
			'showinfo'         => ! ( get_option( 'lly_opt_player_showinfo' ) == '1' ),
			'relations'        => ! ( get_option( 'lly_opt_player_relations' ) == '1' ),
			'buttonstyle'      => get_option( 'll_opt_button_style', '' ),
			'controls'         => ! ( get_option( 'lly_opt_player_controls' ) == '1' ),
			'loadpolicy'       => ! ( get_option( 'lly_opt_player_loadpolicy' ) == '1' ),
			'responsive'       => get_option( 'll_opt_load_responsive' ) == '1',
			'thumbnailquality' => $this->thumbnailquality(),
			'preroll'          => get_option( 'lly_opt_player_preroll', '' ),
			'postroll'         => get_option( 'lly_opt_player_postroll', '' ),
			'displaybranding'  => ( get_option( 'll_attribute' ) == 'link' ),
			'loadthumbnail'	 => $this->should_load_thumbnail(),
			'callback'         => '<!--YOUTUBE_CALLBACK-->'
		) );
	}

	function should_load_thumbnail() {
		$thumbnail = get_option('ll_opt_thumbnail_size');
		return $thumbnail == '' || $thumbnail == 'standard' || $thumbnail == 'cover';
	}

 	/**
 	 * Test which thumbnail quality should be used
 	 */
 	function thumbnailquality() {
		global $lazyload_videos_general;
		return $lazyload_videos_general->get_thumbnail_quality();
 	}

 	/**
 	 * Callback
 	 * expects JavaScript code as string
 	 */
 	function callback() {
 		$js = apply_filters( 'lly_set_callback', '' );
 		return $js;
 	}
}
