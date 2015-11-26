<?php
/**
 * @package Frontend
 */
class Lazyload_Videos_Frontend {

	function init() {
		if ( $this->test_if_scripts_should_be_loaded() ) {
			$this->load_lazyload_style();
			add_action( 'wp_head', array( $this, 'load_lazyload_css') );
			add_filter( 'lly_change_options', array( $this, 'set_options' ) );
			add_filter( 'llv_change_options', array( $this, 'set_options' ) );

			if ( defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ) {
				wp_enqueue_script( 'lazyload-video-js', LL_URL . 'js/lazyload-video.js', array( 'jquery' ), LL_VERSION, true );
			} else if ( (get_option('lly_opt') !== '1') && (get_option('llv_opt') !== '1') ) {
				wp_enqueue_script( 'lazyload-video-js', LL_URL . 'js/min/lazyload-all.min.js', array( 'jquery' ), LL_VERSION, true );
			}

            $settings = array();
            
            if (get_option('lly_opt') !== '1') {
                require( LL_PATH . 'frontend/class-youtube.php' );
                $youtube = new Lazyload_Videos_Youtube();
                $youtube->init();
                
                $settings_youtube = array(
                    'youtube' => $youtube->get_js_settings()
                );
                $settings = array_merge($settings, $settings_youtube);
                
            }
            if (get_option('llv_opt') !== '1') {
                require( LL_PATH . 'frontend/class-vimeo.php' );
                $vimeo = new Lazyload_Video_Vimeo();
                $vimeo->init();
                
                $settings_vimeo = array(
                    'vimeo' => $vimeo->get_js_settings()
                );
                $settings = array_merge($settings, $settings_vimeo);
            }

			wp_localize_script( 'lazyload-video-js', 'lazyload_video_settings', $settings );
		}
	}
    
	/**
	 * Add stylesheet
	 */
	function load_lazyload_style() {
		wp_register_style( 'lazyload-style', plugins_url('css/min/style-lazyload.min.css', plugin_dir_path( __FILE__ )) );
		wp_enqueue_style( 'lazyload-style' );
	}

	/**
	 * Add CSS
	 */
	function load_lazyload_css() {
		echo '<style type="text/css">';

		$this->load_lazyload_css_thumbnail_size();
		$this->load_lazyload_css_video_titles();
		$this->load_lazyload_css_button_style();
		$this->load_lazyload_css_custom();

		echo '</style>';
	}

	/**
	 * Add Custom CSS
	 */
	function load_lazyload_css_custom() {
		if (stripslashes(get_option('ll_opt_customcss')) != '') {
			echo stripslashes(get_option('ll_opt_customcss'));
		}
	}

	/**
	 * Add CSS for thumbnails
	 */
	function load_lazyload_css_thumbnail_size() {
    	if ( (get_option('ll_opt_thumbnail_size') == 'standard') ) {
    		echo '.entry-content a.lazy-load-youtube, a.lazy-load-youtube, .lazy-load-vimeo { background-size: contain !important; }';
    	}
	}

	/**
	 * Add CSS to hide Video titles
	 */
	function load_lazyload_css_video_titles() {
		// Hide Youtube titles with CSS
    	if ( get_option('lly_opt_title') == false ) {
    		echo '.titletext.youtube { display: none; }';
    	}
	}

	/**
	 * Change play button style
	 */
	function load_lazyload_css_button_style() {
    	if ( get_option('ll_opt_button_style') == 'youtube_button_image' ) {
    		// Display youtube button image
    		echo '.preview-youtube .lazy-load-youtube-div, .lazy-load-vimeo-div { background: url('.plugin_dir_url( __FILE__ ).'../images/play-youtube.png) center center no-repeat; }';	
    		// ... and remove CSS-only content
    		echo $this->load_css_button_selectors() . ' { content: ""; }';
    	}
    	else if ( get_option('ll_opt_button_style') == 'youtube_button_image_red' ) {
    		// Display RED youtube button image
    		echo '.preview-youtube .lazy-load-youtube-div, .lazy-load-vimeo-div { background: url('.plugin_dir_url( __FILE__ ).'../images/play-y-red.png) center center no-repeat; }';	
    		// ... and remove CSS-only content
    		echo $this->load_css_button_selectors() . ' { content: ""; }';
    	}
    	else if (
    			get_option('ll_opt_button_style') == 'css_black'
    			|| get_option('ll_opt_button_style') == 'css_black_pulse'
    		) {
    		echo $this->load_css_button_selectors() . ' { color: #000; text-shadow: none; }';
    		echo $this->load_css_button_selectors(':hover') . ' { text-shadow: none; }';
    	}
	}

	/**
	 * Little helper funtion to return the needed selectors for the play buttons
	 */
	private function load_css_button_selectors( $add = '' ) {
		return '
			.preview-youtube .lazy-load-youtube-div'.$add.':before,
			.preview-youtube .lazy-load-youtube-div'.$add.'::before,
			.preview-vimeo'.$add.':after,
			.preview-vimeo'.$add.'::after
			';
	}

	/**
	 * Don't load scripts on specific circumstances
	 */
	function test_if_scripts_should_be_loaded() {
		global $lazyload_videos_general;
		
		return
			( get_option('ll_opt_load_scripts') != '1' ) ||	// Option "Support for Widgets (Youtube only)" is checked
			( get_option('lly_opt_support_for_widgets') == true ) ||	// Option "Support for Widgets (Youtube only)" is checked
			( is_singular() && ($lazyload_videos_general->test_if_post_or_page_has_embed()) )	// Pages/posts with oembedded media
			//|| ( !is_singular() )	// Everything else (except for pages/posts without oembedded media)
		? true : false;
	}

	/** 
	 * Set options to extend setOptionsYoutube() and setOptionsVimeo() that are used in JS files
	 */
	function set_options( $options ) {
		return $this->set_option_video_seo( $options );
	}

	/**
	 * Set option "videoseo" for setOptionsYoutube() and setOptionsVimeo()
	 */
	function set_option_video_seo( $options ) {
		if ( get_option( 'll_opt_video_seo' ) == '1' ) {
			$options[ 'videoseo' ] = true;
		}

		return $options;
	}

}

function initialize_lazyload_frontend() {
	$frontend = new Lazyload_Videos_Frontend();
	$frontend->init();
}
add_action( 'wp_enqueue_scripts', 'initialize_lazyload_frontend' );
