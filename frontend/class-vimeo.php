<?php
/**
 * @package Lazyload Vimeo
 */
class LAZYLOAD_vimeo extends LAZYLOAD_Frontend {

	function __construct() {
		add_action( 'wp_head', array( $this, 'enable_lazyload_js' ) );
	}

	/**
	 * Lazy Load VIMEO Videos (Load vimeo script and video after clicking on the preview image)
	 * Lazy Load for Vimeo works with URLs that look like: [Any Path]/[Video ID]
	 * Examples:
	 * http://vimeo.com/channels/staffpicks/48851874
	 * http://vimeo.com/48851874
	 * http://vimeo.com/48851874/
	 */
	function enable_lazyload_js() {
		if ( parent::test_if_scripts_should_be_loaded() ) {
			wp_enqueue_script( 'script', plugins_url( '../js/min/lazyload-vimeo-ck.js' , __FILE__ ) );
			
			?>
		    <script type='text/javascript'>
 			var $llv_class = jQuery.noConflict();

			$llv_class(document).ready(function() {	
				setOptionsVimeo({
					playercolour: '<?php if (get_option("llv_opt_player_colour") == "") { echo ""; } else { echo get_option("llv_opt_player_colour"); } ?>',
					<?php do_action( 'llv_set_options' ); ?>
				});
			});

	        function showThumb(data){
				$llv_class("#" + data[0].id).css("background", "#000 url(" + data[0].thumbnail_large + ") center center no-repeat");
		    	<?php if (get_option('llv_opt_title') == true) { ?>
		    		$llv_class("#" + data[0].id).children().children('.titletext.vimeo').text(data[0].title);
		    	<?php } ?>	
	        };
		    </script>
		    <?php
		}
	}

}

function initialize_lazyload_vimeo() {
	$lazyload_vimeo = new LAZYLOAD_vimeo();
}
add_action( 'init', 'initialize_lazyload_vimeo' );