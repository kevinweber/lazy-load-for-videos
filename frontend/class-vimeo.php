<?php
/**
 * @package Lazyload Vimeo
 */
class Lazyload_Video_Vimeo {

	public function init() {
		if ( defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ) {
			wp_enqueue_script( 'lazyload_vimeo_js', LL_URL . 'assets/js/lazyload-vimeo.js', array( 'jquery' ), LL_VERSION, true );
		} else if ( get_option('llv_opt') !== '1' ) {
    	wp_enqueue_script( 'lazyload-video-js', LL_URL . 'assets/js/lazyload-vimeo.js', array( 'jquery' ), LL_VERSION, true );
    }
	}

	/**
	 * Lazy Load VIMEO Videos (Load vimeo script and video after clicking on the preview image)
	 * Lazy Load for Vimeo works with URLs that look like: [Any Path]/[Video ID]
	 * Examples:
	 * http://vimeo.com/channels/staffpicks/48851874
	 * http://vimeo.com/48851874
	 * http://vimeo.com/48851874/
	 */
	function get_js_settings() {
		return apply_filters( 'llv_change_options', array(
			'buttonstyle'  => get_option( 'll_opt_button_style', '' ),
			'playercolour' => get_option( 'llv_opt_player_colour', '' ),
			'responsive'   => get_option( 'll_opt_load_responsive' ) == '1',
			'preroll'      => get_option( 'llv_opt_player_preroll', ''),
			'postroll'     => get_option( 'llv_opt_player_postroll', '' ),
			'show_title'   => get_option( 'llv_opt_title', false ) == true,
			'displaybranding'  => ! (get_option( 'll_display_branding', false ) == false),
			'loadthumbnail'	 => $this->should_load_thumbnail(),
			'callback'     => '<!--VIMEO_CALLBACK-->'
		) );
	}

	function should_load_thumbnail() {
		$thumbnail = get_option('ll_opt_thumbnail_size');
		return $thumbnail == '' || $thumbnail == 'standard' || $thumbnail == 'cover';
	}

	/**
 	 * Callback
 	 * expects JavaScript code as string
 	 */
	function callback() {
		$js = apply_filters( 'llv_set_callback', '' );
		return $js;
	}

}
