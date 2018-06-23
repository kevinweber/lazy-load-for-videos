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
				wp_enqueue_script( 'lazyload-video-js', LL_URL . 'assets/js/lazyload-all.js', array( 'jquery' ), LL_VERSION, true );
			} else if ( (get_option('lly_opt') !== '1') && (get_option('llv_opt') !== '1') ) {
				wp_enqueue_script( 'lazyload-video-js', LL_URL . 'assets/js/lazyload-all.js', array( 'jquery' ), LL_VERSION, true );
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
		wp_register_style( 'lazyload-style', plugins_url('assets/css/lazyload-all.css', plugin_dir_path( __FILE__ )) );
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
		$thumbnail = get_option('ll_opt_thumbnail_size');
		$classlist = '.entry-content a.lazy-load-youtube, a.lazy-load-youtube, .lazy-load-vimeo';

    	if ($thumbnail == 'standard') {
    		echo $classlist . '{ background-size: contain; }';
    	} else if ($thumbnail == 'pattern-dots') {
				echo $classlist . '{
					background-color: #000;
					background-image: radial-gradient(#333 15%, transparent 16%),
					radial-gradient(#333 15%, transparent 16%);
					background-size: 50px 50px;
			    background-position: 0 0, 25px 25px;
				}';
			} else if ($thumbnail == 'pattern-light-s') {
				echo $classlist . '{
					background-color: #ccc;
					background-image:
					radial-gradient(circle at 100% 150%, #ccc 24%, white 25%, white 28%, #ccc 29%, #ccc 36%, white 36%, white 40%, transparent 40%, transparent),
					radial-gradient(circle at 0 150%, #ccc 24%, white 25%, white 28%, #ccc 29%, #ccc 36%, white 36%, white 40%, transparent 40%, transparent),
					radial-gradient(circle at 50% 100%, white 10%, #ccc 11%, #ccc 23%, white 24%, white 30%, #ccc 31%, #ccc 43%, white 44%, white 50%, #ccc 51%, #ccc 63%, white 64%, white 71%, transparent 71%, transparent),
					radial-gradient(circle at 100% 50%, white 5%, #ccc 6%, #ccc 15%, white 16%, white 20%, #ccc 21%, #ccc 30%, white 31%, white 35%, #ccc 36%, #ccc 45%, white 46%, white 49%, transparent 50%, transparent),
					radial-gradient(circle at 0 50%, white 5%, #ccc 6%, #ccc 15%, white 16%, white 20%, #ccc 21%, #ccc 30%, white 31%, white 35%, #ccc 36%, #ccc 45%, white 46%, white 49%, transparent 50%, transparent);
					background-size:100px 50px;
				}';
			} else if ($thumbnail == 'pattern-carbon') {
				echo $classlist . '{
					background:
					linear-gradient(27deg, #151515 5px, transparent 5px) 0 5px,
					linear-gradient(207deg, #151515 5px, transparent 5px) 10px 0px,
					linear-gradient(27deg, #222 5px, transparent 5px) 0px 10px,
					linear-gradient(207deg, #222 5px, transparent 5px) 10px 5px,
					linear-gradient(90deg, #1b1b1b 10px, transparent 10px),
					linear-gradient(#1d1d1d 25%, #1a1a1a 25%, #1a1a1a 50%, transparent 50%, transparent 75%, #242424 75%, #242424);
					background-color: #131313;
					background-size: 20px 20px;
				}';
			} else if ($thumbnail == 'none') {
				// No background, no thumbnail
			} else {
				echo $classlist . '{ background-size: cover; }';
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
    		echo '.preview-youtube .lazy-load-youtube-div, .lazy-load-vimeo-div { background: url('.plugin_dir_url( __FILE__ ).'../assets/play-youtube.png) center center no-repeat; }';
    		// ... and remove CSS-only content
    		echo $this->load_css_button_selectors() . ' { content: ""; }';
    	}
    	else if ( get_option('ll_opt_button_style') == 'youtube_button_image_red' ) {
    		// Display RED youtube button image
    		echo '.preview-youtube .lazy-load-youtube-div, .lazy-load-vimeo-div { background: url('.plugin_dir_url( __FILE__ ).'../assets/play-y-red.png) center center no-repeat; }';
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
