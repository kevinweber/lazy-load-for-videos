<?php
/**
 * @package Admin
 */
class Lazyload_Videos_Update_Posts {

	/**
	 * Use WordPress' built in function to delete oembed caches
	 * Unused by core since WP 4.0.0 (http://developer.wordpress.org/reference/classes/wp_embed/delete_oembed_caches/)
	 *
	 * @since 1.6.2
	 */
	function delete_oembed_caches() {
		global $wp_embed;
		$lazyload_videos_general = new Lazyload_Videos_General();

	    $post_ids = get_posts(
	    	array(
	    		'post_type' => $lazyload_videos_general->get_post_types(),
	    		'posts_per_page' => -1,	// -1 == no limit
	    		'fields' => 'ids',	// Just retrieve a list of IDs (http://thomasgriffinmedia.com/blog/2012/10/optimize-wordpress-queries/)
	    		) );

	    foreach ( $post_ids as $post_id ):
	    	$wp_embed->delete_oembed_caches( $post_id );
	    endforeach;
	}

	/**
	 * Delete cache for a single post
	 * @since 2.0.3
	 */
	function delete_oembed_cache( $post_id ) {
		global $wp_embed;
		$wp_embed->delete_oembed_caches( $post_id );
	}

}