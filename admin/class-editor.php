<?php
/**
 * @package Admin
 */

class Lazy_Load_For_Videos_Editor {

	function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'load_js' ) );
	}

	function load_js() {
		// Enqueue block editor JS
		// To learn how this works, check out https://jschof.com/gutenberg-blocks/using-gutenberg-filters-to-extend-blocks/
		wp_enqueue_script(
			'lazyload_editor_js',
			LL_URL . 'assets/js/editor.js',
			[ 'wp-blocks', 'wp-element', 'wp-editor', 'wp-components' ],
			LL_URL
		);
	}
}

new Lazy_Load_For_Videos_Editor();