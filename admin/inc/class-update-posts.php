<?php
/**
 * @package Admin
 */
class lazyload_Update_Posts {

	/**
	 * Use WordPress' built in function to delete oembed caches
	 * Performs much better than the old lazyload_update_posts_with_oembed();
	 *
	 * @since 1.6.2
	 */
	function delete_oembed_caches() {
		global $wp_embed;
	    
	    $arr_posts = get_posts( array( 'post_type' => 'post', 'posts_per_page' => -1 ) );	// -1 == no limit

	    foreach ( $arr_posts as $post ):
	    	$wp_embed->delete_oembed_caches( $post->ID );
	    endforeach;
	}

}