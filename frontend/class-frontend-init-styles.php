<?php
/**
 * @package Frontend – Init Styles
 */
class Lazy_Load_For_Videos_Init_Styles {
	/**
	 * Add CSS
	 */
	function init() {
		wp_enqueue_style( 'lazyload-video-css' );
		
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
    		echo '.lazy-load-div { background: url('.plugin_dir_url( __FILE__ ).'../assets/play-youtube.png) center center no-repeat; }';
    		// ... and remove CSS-only content
    		echo $this->load_css_button_selectors() . ' { content: ""; }';
    	}
    	else if ( get_option('ll_opt_button_style') == 'youtube_button_image_red' ) {
    		// Display RED youtube button image
    		echo '.lazy-load-div { background: url('.plugin_dir_url( __FILE__ ).'../assets/play-y-red.png) center center no-repeat; }';
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
		return ".lazy-load-div{$add}:before";
	}
}
