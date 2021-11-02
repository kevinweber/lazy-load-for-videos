<?php
class KW_LLV_Editor {

	function __construct() {
		if (!function_exists('register_block_type')) {
			// Gutenberg isn't supported
			return;
		}

		add_action( 'enqueue_block_editor_assets', array( $this, 'init' ) );
	}

	function init() {
		$isYoutubeEnabled = get_option('lly_opt') !== '1';
		$isVimeoEnabled = get_option('llv_opt') !== '1';

		if (!$isYoutubeEnabled && !$isVimeoEnabled) return;

		wp_enqueue_script(
			'lazyload-editor-js',
			LL_URL . 'public/js/editor.js',
			[ 'wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'lodash' ],
			SCRIPT_DEBUG ? null : LL_VERSION
		);

		wp_enqueue_style(
			'lazyload-editor-css',
			LL_URL . 'public/css/lazyload-shared.css',
			[],
			SCRIPT_DEBUG ? null : LL_VERSION
		);

		require_once( LL_PATH . 'src/php/static-styles.php' );
		KW_LLV_Styles::enqueue();

		if ($isYoutubeEnabled) {
			$this->initScriptYoutube();
		}
		if ($isVimeoEnabled) {
			$this->initScriptVimeo();
		}
	}

	function initScriptYoutube() {
		require_once( LL_PATH . 'src/php/static-youtube.php' );
		wp_add_inline_script(
			'lazyload-editor-js',
			KW_LLV_Youtube::get_inline_script(),
			'before'
		);
	}

	function initScriptVimeo() {
		require_once( LL_PATH . 'src/php/static-vimeo.php' );
		wp_add_inline_script(
			'lazyload-editor-js',
			KW_LLV_Vimeo::get_inline_script(),
			'before'
		);
	}
}

new KW_LLV_Editor();
