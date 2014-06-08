<?php
/**
 * @package Frontend
 */
	/**
	 * Enable oEmbed dataparse for widgets
	 */
	add_filter( 'widget_text', array( $wp_embed, 'run_shortcode' ), 8 );
	add_filter( 'widget_text', array( $wp_embed, 'autoembed'), 8 );