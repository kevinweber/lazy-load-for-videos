<?php
/**
 * @package Admin
 */
class lazyload_Update_Posts {

	/**
	 * Update all posts that have an oembedded medium
	 */
	function lazyload_update_posts_with_oembed(){
			// $lazyload_general = new LAZYLOAD_General();

		 //    $arr_posts = get_posts( array( 'post_type' => 'post', 'posts_per_page' => -1 ) );	// -1 == no limit

		 //    foreach ( $arr_posts as $post ):
		 //    	if ( $lazyload_general->test_if_post_or_page_has_embed( $post->ID ) ) {
		 //    		wp_update_post( $post );
		 //    	}
		 //    endforeach;

		$this->delete_oembed_caches();
	}

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