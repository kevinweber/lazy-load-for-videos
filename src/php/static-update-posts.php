<?php
class Lazy_Load_For_Videos_Update_Posts {

	/**
	 * Delete post-specific metadata that was added by this plugin.
	 *
	 * @since 2.9.0
	 */
	static function delete_postmeta() {
		global $wpdb;
		$meta_key_1 = "lazyload_thumbnail_quality";
		$wpdb->query(
	        $query = $wpdb->prepare( 
                "DELETE FROM `".$wpdb->postmeta."`
                    WHERE meta_key = %s",
	          	$meta_key_1
	        )
	    );
	}

	/**
	 * WordPress' built in function to delete oembed caches is too slow for mass updates, 
	 * and it is unused by core since WP 4.0.0 (http://developer.wordpress.org/reference/classes/wp_embed/delete_oembed_caches/)
	 *
	 * @since 1.6.2
	 */
	static function delete_oembed_caches() {
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
		
		// Flush all transient oembed caches. Those are used by the block editor.
		// @since 2.9.0
		$option_name_1 = "|_transient|_oembed|_%%";
		$option_name_2 = "|_transient|_timeout|_oembed|_%%";
		$wpdb->query(
	        $query = $wpdb->prepare( 
                "DELETE FROM `".$wpdb->options."`
                    WHERE `option_name` LIKE %s ESCAPE '|'
					OR `option_name` LIKE %s ESCAPE '|'",
	          	$option_name_1,
	          	$option_name_2
	        )
	    );
	}

	/**
	 * Delete cache for a single post
	 * @since 2.0.3
	 */
	static function delete_oembed_cache( $post_id ) {
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