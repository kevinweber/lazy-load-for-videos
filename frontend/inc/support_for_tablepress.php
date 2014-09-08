<?php
/**
 * @package Frontend
 */
	/**
	 * Enable oEmbed dataparse for WordPress plugin "TablePress"
	 */
	global $wp_embed;
	add_filter( 'tablepress_cell_content', array( $wp_embed, 'run_shortcode' ), 12 );
	add_filter( 'tablepress_cell_content', array( $wp_embed, 'autoembed'), 12 );