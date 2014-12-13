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
		global $wpdb;
		$meta_key_1 = "|_oembed|_%%";
		$meta_key_2 = "|_oembed|_time|_%%";
		$wpdb->query(
	        $query = $wpdb->prepare( 
                "DELETE FROM `".$wpdb->postmeta."`
                    WHERE `meta_key` LIKE %s ESCAPE '|'
                        OR `meta_key` LIKE %s ESCAPE '|'",
	          	$meta_key_1,
	          	$meta_key_2
	        )
	    );
	}

	/**
	 * Delete cache for a single post
	 * @since 2.0.3
	 */
	function delete_oembed_cache( $post_id ) {
		global $wpdb;
		$meta_key_1 = "|_oembed|_%%";
		$meta_key_2 = "|_oembed|_time|_%%";
		$wpdb->query(
	        $query = $wpdb->prepare( 
                "DELETE FROM `".$wpdb->postmeta."`
                    WHERE post_id = %d
                    	AND (
                    		`meta_key` LIKE %s ESCAPE '|'
                        	OR `meta_key` LIKE %s ESCAPE '|'
                        	)",
	          	$post_id,
	          	$meta_key_1,
	          	$meta_key_2
	        )
	    );
	}

}