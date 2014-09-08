<?php
/**
 * @package Frontend
 */
	/**
	 * Enable oEmbed dataparse for widgets
	 */
	global $wp_embed;
	add_filter( 'widget_text', array( $wp_embed, 'run_shortcode' ), 8 );
	add_filter( 'widget_text', array( $wp_embed, 'autoembed'), 8 );