<?php

class Lazy_Load_For_Videos_General {

	function has_post_or_page_embed( $post_id ) {
		
	 	// Get meta keys for current post
	    $post_meta = get_post_custom_keys( $post_id );
	 
		if ( empty($post_meta) )
			return false;

		$has_youtube = has_post_embed_with_value($post_id, "%youtube%");
		$has_vimeo = has_post_embed_with_value($post_id, "%vimeo%");

	 	// Look every meta_key that begins with an oembed string,
		// then check if the associated value includes "youtube" or "vimeo"
		if ( is_array( $post_meta ) || $post_meta instanceof Traversable ) {
		    foreach( $post_meta as $meta ) {
		    	$first_seven_chars = substr( trim( $meta ) , 0 , 7 );
		    	if ( $first_seven_chars == 'oembed_' ||
		    		 $first_seven_chars == '_oembed' ) {
		    		return true;
		    	} // '_oembed' is used by WordPress standardly; 'oembed_' is used by some plugins and themes
		    }
		    return false;
		} else {
			return false;
	    }
	}

}

$lazyload_videos_general = new Lazy_Load_For_Videos_General();