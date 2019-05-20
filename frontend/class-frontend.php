<?php require_once( LL_PATH . 'frontend/class-frontend-css.php' );

/**
 * @package Frontend
 */
function lazyload_videos_register_scripts() {
	// wp_deregister_script('jquery'); // <= For development: Deregister jQuery to ensure this plugin works without jQuery
	wp_register_style( 'lazyload-video-css', LL_URL . 'assets/css/lazyload-all.css' );
	wp_register_script( 'lazyload-video-js', LL_URL . 'assets/js/lazyload-all.js', null, LL_VERSION, true );
	wp_register_script( 'lazyload_vimeo_js', LL_URL . 'assets/js/lazyload-vimeo.js', null, LL_VERSION, true );
	wp_register_script( 'lazyload_youtube_js', LL_URL . 'assets/js/lazyload-youtube.js', null, LL_VERSION, true );
}

add_action( 'wp_enqueue_scripts', 'lazyload_videos_register_scripts' );

class Lazy_Load_For_Videos_Init_Scripts {

	private $enqueued_css = false;
	private $enqueued_vimeo = false;
	private $enqueued_youtube = false;

	function load_lazyload_css() {
		if ($this->enqueued_css) return;

		$css = new Lazy_Load_For_Videos_Init_CSS();
		$css->enqueue();
		$this->enqueued_css = true;
	}

	function init() {
		if (!$this->should_scripts_be_loaded()) return;

		$this->load_lazyload_css();

		$isDebugging = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG;
		$areBothVideosEnabled = (get_option('lly_opt') !== '1') && (get_option('llv_opt') !== '1');

		if ($isDebugging || $areBothVideosEnabled) {
			// "lazyload-video-js" is a script combining Vimeo and Youtube
			wp_enqueue_script( 'lazyload-video-js');
			$this->enqueued_vimeo = true;
			$this->enqueued_youtube = true;
		}

		$settings = array();

		if (get_option('lly_opt') !== '1') {
			require( LL_PATH . 'frontend/class-youtube.php' );
			$youtube = new Lazy_Load_For_Videos_Youtube();
			
			if (!$this->enqueued_youtube) {
				$youtube->enqueue();
				$this->enqueued_youtube = true;
			}

			$settings_youtube = array(
				'youtube' => $youtube->get_js_settings()
			);
			$settings = array_merge($settings, $settings_youtube);

		}
		
		if (get_option('llv_opt') !== '1') {
			require( LL_PATH . 'frontend/class-vimeo.php' );
			$vimeo = new Lazy_Load_For_Videos_Vimeo();

			if (!$this->enqueued_vimeo) {
				$vimeo->enqueue();
				$this->enqueued_vimeo = true;
			}

			$settings_vimeo = array(
				'vimeo' => $vimeo->get_js_settings()
			);
			$settings = array_merge($settings, $settings_vimeo);
		}

		wp_localize_script( 'lazyload-video-js', 'lazyload_video_settings', $settings );
		wp_localize_script( 'lazyload_vimeo_js', 'lazyload_video_settings', $settings );
		wp_localize_script( 'lazyload_youtube_js', 'lazyload_video_settings', $settings );
	}

	/**
	 * Don't load scripts in specific circumstances
	 */
	function should_scripts_be_loaded() {
		if (
			( get_option('ll_opt_load_scripts') != '1' ) ||	// Option "Only load CSS/JS when needed" is NOT checked
			( get_option('lly_opt_support_for_widgets') == true ) // Always load scripts if widgets need lazy load support (Youtube only)
		) {
			return true;
		}
		
		global $lazyload_videos_general;
		if (is_singular()) {
			$post_id = absint(get_the_ID());
			return $lazyload_videos_general->has_post_or_page_embed($post_id);
		}
		
		// For pages with multiple posts (e.g. homepage and archives),
		// iterate over all posts to see if any of them includes an embed.
		global $posts;
		foreach($posts as $post) {
			$has_post_embed = $lazyload_videos_general->has_post_or_page_embed($post->ID);
			if ($has_post_embed) return true;
		};

		return false;
	}
}

function lazyload_videos_frontend() {
	$scripts = new Lazy_Load_For_Videos_Init_Scripts();
	$scripts->init();
}

add_action( 'wp_enqueue_scripts', 'lazyload_videos_frontend' );
