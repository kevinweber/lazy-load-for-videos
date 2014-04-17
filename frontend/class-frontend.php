<?php
/**
 * @package Frontend
 */
class LAZYLOAD_Frontend {

	function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'lazyload_enqueue_jquery' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'load_lazyload_style') );
		add_action( 'wp_head', array( $this, 'load_lazyload_custom_css') );
		add_filter('oembed_dataparse', array( $this, 'lazyload_replace_video' ), 10, 3);
		add_action('wp_head', array( $this, 'enable_lazyload_js' ) );
	}

	/**
	 * Enable jQuery (comes with WordPress)
	 */
	function lazyload_enqueue_jquery() {
    	wp_enqueue_script('jquery');
	}

	/**
	 * Add stylesheet
	 */
	function load_lazyload_style() {
		wp_register_style( 'lazyload-style', plugins_url('css/min/style-lazyload.css', plugin_dir_path( __FILE__ )) );
		wp_enqueue_style( 'lazyload-style' );
	}

	/**
	 * Add Custom CSS
	 */
	function load_lazyload_custom_css(){ ?>
		<style type="text/css">	
			<?php if (stripslashes(get_option('ll_opt_customcss')) != '') { ?>
				<?php echo stripslashes(get_option('ll_opt_customcss')); ?>
			<?php } ?>
		</style>
	<?php
	}

	/**
	 * Replace embedded Youtube and Vimeo videos with a special piece of code.
	 * Thanks to Otto's comment on StackExchange (See http://wordpress.stackexchange.com/a/19533)
	 */
	function lazyload_replace_video($return, $data, $url) {

		// Youtube support
	    if ( (! is_feed()) && ($data->provider_name == 'YouTube') 
				&& (get_option('lly_opt') == false) // test if Lazy Load for Youtube is deactivated
	    	) {
	    	// Test: Display Youtube title
	    	if ( (get_option('lly_opt_title') == true) ) {
	    		$titletxt = $data->title;
	    	}
	    	else {
	    		$titletxt = '&ensp;';
	    	}

       		$preview_url = '<a class="lazy-load-youtube preview-youtube" href="' . $url . '" title="Play Video &quot;' . $data->title . '&quot;">'
	       		. $titletxt .
	       		'</a>';
       		return $preview_url;
	    }

	    // Vimeo support
	    elseif ( (! is_feed()) && ($data->provider_name == 'Vimeo') 
				&& (get_option('llv_opt') == false) // test if Lazy Load for Vimeo is deactivated
	    	) {

			$spliturl = explode("/", $url);
			foreach($spliturl as $key=>$value)
			{
			    if ( empty( $value ) )
			        unset($spliturl[$key]);
			};
			$vimeoid = end($spliturl);

			$preview_url = '<div id="' . $vimeoid . '" class="lazy-load-vimeo preview-vimeo" title="Play Video &quot;' . $data->title . '&quot;">
					
				</div>';
       		return $preview_url;
	    }

	    else return $return;
	}

	function enable_lazyload_js() {}

}


function initialize_lazyload_frontend() {
	$lazyload_frontend = new LAZYLOAD_Frontend();
}
add_action( 'init', 'initialize_lazyload_frontend' );