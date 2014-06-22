<?php
/**
 * register_activation_hook() and register_deactivation_hook() MUST NOT be called with action 'plugins_loaded' or any 'admin_init'
 * @package Admin
 */

register_activation_hook( LL_FILE, 'lazyload_plugin_activation' );
register_deactivation_hook( LL_FILE, 'lazyload_plugin_deactivation' );

function lazyload_plugin_activation() {
	lazyload_update_posts_with_oembed();

	$signup = '<div id="mc_embed_signup">
			<form action="'.LL_NEWS_ACTION_URL.'" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
				<div class="mc-field-group">
					<label for="mce-EMAIL" style="line-height:2.5em">'.LL_NEWS_TEXT.'</label><br>
					<input type="email" value="Enter your email address" name="EMAIL" class="required email" id="mce-EMAIL" onclick="this.focus();this.select()" onfocus="if(this.value == \'\') { this.value = this.defaultValue; }" onblur="if(this.value == \'\') { this.value = this.defaultValue; }">
					<input type="hidden" name="GROUPS" id="GROUPS" value="'.LL_NEWS_GROUP.'" />
					<input type="submit" value="'.LL_NEWS_BUTTON.'" name="subscribe" id="mc-embedded-subscribe" class="button">
				</div>
				<div id="mce-responses" class="clear">
					<div class="response" id="mce-error-response" style="display:none"></div>
					<div class="response" id="mce-success-response" style="display:none"></div>
				</div>
			    <div style="position: absolute; left: -5000px;"><input type="text" name="'.LL_NEWS_NAME.'" tabindex="-1" value=""></div>
			</form>
			</div>';


	$notices = get_option( 'lazyload_deferred_admin_notices', array() );
	$notices[] = $signup . '<br>Edit your plugin settings: <strong>
					<a href="options-general.php?page=lazyload.php">Lazy Load for Videos</a>
					</strong>';
				;
	update_option( 'lazyload_deferred_admin_notices', $notices );
}

function lazyload_plugin_deactivation() {
	lazyload_update_posts_with_oembed();
	delete_option( 'lazyload_deferred_admin_notices' );
}

/**
 * Update all posts that have an oembedded medium
 */
function lazyload_update_posts_with_oembed(){
		$lazyload_general = new LAZYLOAD_General();

	    $arr_posts = get_posts( array( 'post_type' => 'post', 'posts_per_page' => -1 ) );

	    foreach ( $arr_posts as $post ):
	    	if ( $lazyload_general->test_if_post_or_page_has_embed( $post->ID ) ) {
	    		wp_update_post( $post );
	    	}
	    endforeach;
}

class lazyload_Register {

	function __construct() {
		add_action( 'admin_notices', array( $this, 'lazyload_plugin_notice_activation' ) );
	}

	/**
	 * Display notification when plugin is activated
	 */
	function lazyload_plugin_notice_activation() {
	  if ( $notices = get_option( 'lazyload_deferred_admin_notices' ) ) {
	    foreach ($notices as $notice) {
	      echo "<div class='updated'><p>$notice</p></div>";
	    }
	    delete_option( 'lazyload_deferred_admin_notices' );
	  }
	}

}

function initialize_lazyload_register() {
	$lazyload_admin = new lazyload_Register();
}
add_action( 'init', 'initialize_lazyload_register' );