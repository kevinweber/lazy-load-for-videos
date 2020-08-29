<?php
class Lazy_Load_For_Videos_General {

	// Don't change those strings since exactly those strings are needed by the Youtube JavaScript file
	private $js_thumbnailquality_default = '0';
	private $js_thumbnailquality_maxresdefault = 'maxresdefault';

	function has_post_or_page_embed( $post_id ) {
		
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

		if (!isset($post->ID)) {
			$id = null;
		}
		else {
			$id = $post->ID;
		}

		// When the individual status for a page/post is '0', all the other settings don't matter.
		$post_thumbnail_quality = get_post_meta( $id, 'lazyload_thumbnail_quality', true );
		if (
			$post_thumbnail_quality === 'max'
			|| ( empty($post_thumbnail_quality) && ( get_option('lly_opt_thumbnail_quality') === 'max' ) )
			// Need to check for "default" value for backward compatibility because this plugin used to store "default" in the DB,
			// and now we're not storing any value in the default case anymore.
			// See: https://github.com/kevinweber/lazy-load-for-videos/pull/48/files#diff-a7050d7d07c23aab4907f6e32ef248cdR101
			|| ( $post_thumbnail_quality === 'default' && ( get_option('lly_opt_thumbnail_quality') === 'max' ) )
			) {
			return $this->js_thumbnailquality_maxresdefault;
		}

		return $this->js_thumbnailquality_default;
 	}

}

$lazyload_videos_general = new Lazy_Load_For_Videos_General();