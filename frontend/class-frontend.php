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

}


function initialize_lazyload_frontend() {
	$lazyload_frontend = new LAZYLOAD_Frontend();
}
add_action( 'init', 'initialize_lazyload_frontend' );