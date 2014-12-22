<?php
/**
 * @package Lazyload Youtube
 */
class Lazyload_Videos_Youtube extends Lazyload_Videos_Frontend {

	function __construct() {
		add_action( 'wp_head', array( $this, 'enable_lazyload_js' ) );
		parent::enable_lazyload_js_init();
	}

	/**
	 * Lazy Load Youtube Videos (Load youtube script and video after clicking on the preview image)
	 * Thanks to »Lazy loading of youtube videos by MS-potilas 2012« (see http://yabtb.blogspot.com/2012/02/youtube-videos-lazy-load-improved-style.html)
	 */
	function enable_lazyload_js() {
		if ( parent::test_if_scripts_should_be_loaded() && (get_option('lly_opt') !== '1') ) {
			if ( defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ) {
				wp_enqueue_script( 'lazyload_youtube_js', plugins_url( '../js/lazyload-youtube.js' , __FILE__ ), array( 'jquery' ), LL_VERSION );
			} else {
				wp_enqueue_script( 'lazyload_youtube_js', plugins_url( '../js/min/lazyload-youtube-ck.js' , __FILE__ ), array( 'jquery' ), LL_VERSION );
			} ?>
			<script>
			(function ( $ ) {

				$(document).ready(function() {
					lazyload_youtube.init({
						theme: '<?php if (get_option("lly_opt_player_colour") == "") { echo "dark"; } else { echo get_option("lly_opt_player_colour"); } ?>',
						colour: '<?php if (get_option("lly_opt_player_colour_progress") == "") { echo "red"; } else { echo get_option("lly_opt_player_colour_progress"); } ?>',
						showinfo: <?php if (get_option("lly_opt_player_showinfo") == "1") { echo "false"; } else { echo "true"; } ?>,
						relations: <?php if (get_option("lly_opt_player_relations") == "1") { echo "false"; } else { echo "true"; } ?>,
						buttonstyle: '<?php if (get_option("ll_opt_button_style") == "") { echo ""; } else { echo get_option("ll_opt_button_style"); } ?>',
						controls: <?php if (get_option("lly_opt_player_controls") == "1") { echo "false"; } else { echo "true"; } ?>,
						loadpolicy: <?php if (get_option("lly_opt_player_loadpolicy") == "1") { echo "false"; } else { echo "true"; } ?>,
						responsive: <?php if (get_option("ll_opt_load_responsive") == "1") { echo "true"; } else { echo "false"; } ?>,
						thumbnailquality: '<?php echo $this->thumbnailquality(); ?>',
						<?php do_action( 'lly_set_options' ); ?>
					});
				});

			})(jQuery);
			</script>
			<?php
		}
	}

 	/**
 	 * Test which thumbnail quality should be used
 	 */
 	function thumbnailquality() {
		global $lazyload_videos_general;
		return $lazyload_videos_general->get_thumbnail_quality();
 	}

}

function initialize_lazyload_youtube() {
	new Lazyload_Videos_Youtube();
}
add_action( 'init', 'initialize_lazyload_youtube' );