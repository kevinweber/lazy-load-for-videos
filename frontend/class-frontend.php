<?php
/**
 * @package Frontend
 */
class LAZYLOAD_Frontend {

	function __construct() {
		require_once( LL_PATH . 'frontend/class-youtube.php' );
		require_once( LL_PATH . 'frontend/class-vimeo.php' );
		add_action( 'wp_enqueue_scripts', array( $this, 'load_lazyload_style') );
		add_action( 'wp_head', array( $this, 'load_lazyload_custom_css') );
	}

	/**
	 * Add stylesheet
	 */
	function load_lazyload_style() {
		if ( $this->test_if_scripts_should_be_loaded() ) {
			wp_enqueue_script( 'jquery' ); // Enable jQuery (comes with WordPress)
			wp_register_style( 'lazyload-style', plugins_url('css/min/style-lazyload.css', plugin_dir_path( __FILE__ )) );
			wp_enqueue_style( 'lazyload-style' );
		}
	}

	/**
	 * Add Custom CSS
	 */
	function load_lazyload_custom_css() {
		echo '<style type="text/css">';
			if (stripslashes(get_option('ll_opt_customcss')) != '') {
				echo stripslashes(get_option('ll_opt_customcss'));
			}
	    	if ( (get_option('ll_opt_thumbnail_size') == 'cover') ) {
	    		echo 'a.lazy-load-youtube, .lazy-load-vimeo { background-size: cover !important; }';
	    	}
		echo '</style>';
	}

	/**
	 * Don't load scripts on specific circumstances
	 */
	function test_if_scripts_should_be_loaded() {
		$lazyload_general = new LAZYLOAD_General();

		return
			( get_option('lly_opt_support_for_widgets') == true ) ||	// Option "Support for Widgets (Youtube only)" is checked
			( is_singular() && ($lazyload_general->test_if_post_or_page_has_embed()) ) ||	// Pages/posts with oembedded media
			( !is_singular() )	// Everything else (except for pages/posts without oembedded media)
		? true : false;
	}

}

$lazyload_frontend = new LAZYLOAD_Frontend();