<?php
/**
 * @package General (needed for both: admin and frontend)
 */
class Lazyload_Videos_General {

	// Don't change those strings since exactly those strings are needed by the Youtube JavaScript file
	private $thumbnailquality_default = '0';
	private $thumbnailquality_maxresdefault = 'maxresdefault';

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

	/**
	 * Set supported post types
	 * @return array()
	 * @since 2.0.4
	 */
	private function set_post_types() {
		$post_types = get_post_types();
		$post_types = apply_filters( 'lazyload_videos_post_types' , $post_types );

		return $post_types;
	}

	/**
	 * Get supported post types
	 * @return array()
	 * @since 2.0.4
	 */
	function get_post_types() {
		$post_types = $this->set_post_types();
		return $post_types;
	}

 	/**
 	 * Test which thumbnail quality should be used
 	 */
 	function get_thumbnail_quality() {
		global $post;
		$thumbnailquality = $this->thumbnailquality_default;

		if (!isset($post->ID)) {
			$id = null;
		}
		else {
			$id = $post->ID;
		}

		// When the individual status for a page/post is '0', all the other settings don't matter.
		if ( get_post_meta( $id, 'lazyload_thumbnail_quality', true ) && get_post_meta( $id, 'lazyload_thumbnail_quality', true ) === $this->thumbnailquality_default ) {
			return $thumbnailquality;
		}
		elseif (
			( get_post_meta( $id, 'lazyload_thumbnail_quality', true ) && get_post_meta( $id, 'lazyload_thumbnail_quality', true ) === 'max' )
			|| ( ( get_post_meta( $id, 'lazyload_thumbnail_quality', true ) !== $this->thumbnailquality_default ) && ( get_option('lly_opt_thumbnail_quality') === 'max' ) )
			) {
			$thumbnailquality = $this->thumbnailquality_maxresdefault;
		}

		return $thumbnailquality;
 	}

}

$lazyload_videos_general = new Lazyload_Videos_General();