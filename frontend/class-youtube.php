<?php
/**
 * @package Lazyload Youtube
 */
class LAZYLOAD_youtube {

	function __construct() {
		add_action( 'wp_head', array( $this, 'enable_lazyload_js' ) );
	}

	/**
	 * Lazy Load Youtube Videos (Load youtube script and video after clicking on the preview image)
	 * Thanks to »Lazy loading of youtube videos by MS-potilas 2012« (see http://yabtb.blogspot.com/2012/02/youtube-videos-lazy-load-improved-style.html)
	 */
	function enable_lazyload_js() {
		wp_enqueue_script( 'lazyload_youtube_js', plugins_url( '../js/min/lazyload-youtube-ck.js' , __FILE__ ) );

		?>
		<script>
			var $ind = jQuery.noConflict();

			$ind(document).ready(function() {
				setOptions({
					theme: '<?php if (get_option("select_bubble_style") == "") { echo "dark"; } else { echo get_option("lly_opt_player_colour"); } ?>',
				});
			});
		</script>
		<?php
	}

}

function initialize_lazyload_youtube() {
	$lazyload_youtube = new LAZYLOAD_youtube();
}
add_action( 'init', 'initialize_lazyload_youtube' );