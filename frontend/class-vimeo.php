<?php
/**
 * @package Lazyload Vimeo
 */
class Lazyload_Video_Vimeo {

	public function init() {
		if ( defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ) {
			wp_enqueue_script( 'lazyload_vimeo_js', LL_URL . 'js/lazyload-vimeo.js', array( 'lazyload-video-js' ), LL_VERSION, true );
		}

		add_action( 'lazyload_videos_js', array( $this, 'enable_lazyload_js' ) );
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
		?>
		(function ( $ ) {

			$(document).ready(function() {	
				lazyload_vimeo.init({
					buttonstyle: '<?php if (get_option("ll_opt_button_style") == "") { echo ""; } else { echo get_option("ll_opt_button_style"); } ?>',
					playercolour: '<?php if (get_option("llv_opt_player_colour") == "") { echo ""; } else { echo get_option("llv_opt_player_colour"); } ?>',
					responsive: <?php if (get_option("ll_opt_load_responsive") == "1") { echo "true"; } else { echo "false"; } ?>,
					preroll: '<?php if (get_option("llv_opt_player_preroll") == "") { echo ""; } else { echo get_option("llv_opt_player_preroll"); } ?>',
					postroll: '<?php if (get_option("llv_opt_player_postroll") == "") { echo ""; } else { echo get_option("llv_opt_player_postroll"); } ?>',
					<?php do_action( 'llv_set_options' ); ?>
					callback: function(){ <?php echo $this->callback(); ?> },
				});
			});

		})(jQuery);

		function showThumb(data){
			jQuery("#" + data[0].id).css("background", "#000 url(" + data[0].thumbnail_large + ") center center no-repeat");
			<?php if (get_option('llv_opt_title') == true) { ?>
				jQuery("#" + data[0].id).children().children('.titletext.vimeo').text(data[0].title);
			<?php } ?>	
		};
		<?php
	}

	/**
 	 * Callback
 	 * expects JavaScript code as string
 	 */
	function callback() {
		$js = apply_filters( 'llv_set_callback', '' );
		return $js;
	}

}
