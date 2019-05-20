<?php
/**
 * @package Frontend – Init Scripts
 */
class Lazy_Load_For_Videos_Init_Scripts {
	private $enqueued_vimeo = false;
	private $enqueued_youtube = false;

	function init() {
		$isYoutubeEnabled = get_option('lly_opt') !== '1';
		$isVimeoEnabled = get_option('llv_opt') !== '1';
		
		if ($isYoutubeEnabled && $isVimeoEnabled) {
			// "lazyload-video-js" is a script combining Vimeo and Youtube
			wp_enqueue_script( 'lazyload-video-js');
			$this->enqueued_vimeo = true;
			$this->enqueued_youtube = true;
		}

		$settings = array();

		if ($isYoutubeEnabled) {
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
		
		if ($isVimeoEnabled) {
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
		wp_localize_script( 'lazyload-vimeo-js', 'lazyload_video_settings', $settings );
		wp_localize_script( 'lazyload-youtube-js', 'lazyload_video_settings', $settings );
	}
}
