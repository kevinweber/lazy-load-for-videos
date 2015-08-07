<?php
/**
 * @package Lazyload Youtube
 */
class Lazyload_Videos_Youtube {

	public function init() {
		if ( defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ) {
			wp_enqueue_script( 'lazyload_youtube_js', LL_URL . 'js/lazyload-youtube.js', array( 'lazyload-video-js' ), LL_VERSION, true );
		}

		add_action( 'lazyload_videos_js', array( $this, 'enable_lazyload_js' ) );
	}

	/**
	 * Lazy Load Youtube Videos (Load youtube script and video after clicking on the preview image)
	 * Thanks to »Lazy loading of youtube videos by MS-potilas 2012« (see http://yabtb.blogspot.com/2012/02/youtube-videos-lazy-load-improved-style.html)
	 */
	function enable_lazyload_js() {
		?>
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
					preroll: '<?php if (get_option("lly_opt_player_preroll") == "") { echo ""; } else { echo get_option("lly_opt_player_preroll"); } ?>',
					postroll: '<?php if (get_option("lly_opt_player_postroll") == "") { echo ""; } else { echo get_option("lly_opt_player_postroll"); } ?>',
					<?php do_action( 'lly_set_options' ); ?>
					callback: function(){ <?php echo $this->callback(); ?> },
				});
			});

		})(jQuery);
		<?php
	}

 	/**
 	 * Test which thumbnail quality should be used
 	 */
 	function thumbnailquality() {
		global $lazyload_videos_general;
		return $lazyload_videos_general->get_thumbnail_quality();
 	}

 	/**
 	 * Callback
 	 * expects JavaScript code as string
 	 */
 	function callback() {
 		$js = apply_filters( 'lly_set_callback', '' );
 		return $js;
 	}
}
