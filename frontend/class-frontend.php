<?php
/**
 * @package Frontend
 */
class LAZYLOAD_Frontend {

	function __construct() {
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
		return
			( is_singular() && ($this->test_if_post_or_page_has_embed()) ) ||	// Pages/posts with oembedded media
			( !is_singular() )	// Everything else (except for pages/posts without oembedded media)
		? true : false;
	}		

	/**
	 * Thanks to http://t31os.wordpress.com/2010/05/24/post-has-embed/ for a nicer solution than mine
	 */
	function test_if_post_or_page_has_embed( $post_id = false ) {
	    if( !$post_id )
	        $post_id = get_the_ID();
	    else
	        $post_id = absint( $post_id );
	    if( !$post_id )
	        return false;

	 	// Get meta keys for current post
	    $post_meta = get_post_custom_keys( $post_id );
	 
	 	// Search for the first meta_key [$value] that begins with the oembed string [$string]
		// After the first hits: continue to return true
	    foreach( $post_meta as $meta ) {
	        if( '_oembed' != substr( trim( $meta ) , 0 , 7 ) )
	            continue;
	        return true;
	    }
	    return false;
	}

}

$lazyload_frontend = new LAZYLOAD_Frontend();