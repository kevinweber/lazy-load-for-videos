<?php
/**
 * @package General (needed for both: admin and frontend)
 */
class LAZYLOAD_General {
	/**
	 * Thanks to http://t31os.wordpress.com/2010/05/24/post-has-embed/ for a nicer solution than mine
	 */
	function test_if_post_or_page_has_embed( $post_id = false ) {
	    if( !$post_id )
	        $post_id = get_the_ID();
	    else
	        $post_id = absint( $post_id );
	    if( !$post_id )
	        return false;

	 	// Get meta keys for current post
	    $post_meta = get_post_custom_keys( $post_id );
	 
		if ( empty($post_meta) )
			return false;

	 	// Search for the first meta_key [$value] that begins with the oembed string [$string]
		// After the first hits: continue to return true
		if ( is_array( $post_meta ) || $post_meta instanceof Traversable ) {
		    foreach( $post_meta as $meta ) {
		        if( ('oembed_' || '_oembed') != substr( trim( $meta ) , 0 , 7 ) )	// '_oembed' is used by WordPress standardly; 'oembed_' is used by some plugins and themes
		            continue;
		        return true;
		    }
		    return false;
		} else {
			return false;
	    }
	}
}