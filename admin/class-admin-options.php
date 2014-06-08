<?php
/**
 * Create options panel (http://codex.wordpress.org/Creating_Options_Pages)
 * @package Admin
 */

class LAZYLOAD_Admin {

	function __construct() {
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		// The 'oembed_dataparse' filter should be called on backend AND on frontend, not only on backend [is_admin()]. Otherwise, on some websites occur errors.
		add_filter( 'oembed_dataparse', array( $this, 'lazyload_replace_video' ), 10, 3 );
		add_action( 'admin_menu', array( $this, 'lazyload_create_menu' ) );
	}

	function admin_init() {
		if ( isset( $_GET['page'] ) && ( $_GET['page'] == 'lazyload.php') ) {
			$this->lazyload_admin_css();
			$this->lazyload_admin_js();
		}
		$this->register_lazyload_settings();
	}

	/**
	 * Replace embedded Youtube and Vimeo videos with a special piece of code.
	 * Thanks to Otto's comment on StackExchange (See http://wordpress.stackexchange.com/a/19533)
	 */
	function lazyload_replace_video($return, $data, $url) {

		// Youtube support
	    if ( (! is_feed()) && ($data->provider_name == 'YouTube') 
				&& (get_option('lly_opt') == false) // test if Lazy Load for Youtube is deactivated
	    	) {
	    	// Test: Display Youtube title
	    	if ( (get_option('lly_opt_title') == true) ) {
	    		$titletxt = $data->title;
	    	}
	    	else {
	    		$titletxt = '&ensp;';
	    	}

       		$preview_url = '<a class="lazy-load-youtube preview-youtube" href="' . $url . '" title="Play Video &quot;' . $data->title . '&quot;">'
	       		. $titletxt .
	       		'</a>';
       		return $preview_url;
	    }

	    // Vimeo support
	    elseif ( (! is_feed()) && ($data->provider_name == 'Vimeo') 
				&& (get_option('llv_opt') == false) // test if Lazy Load for Vimeo is deactivated
	    	) {

			$spliturl = explode("/", $url);
			foreach($spliturl as $key=>$value)
			{
			    if ( empty( $value ) )
			        unset($spliturl[$key]);
			};
			$vimeoid = end($spliturl);

			$preview_url = '<div id="' . $vimeoid . '" class="lazy-load-vimeo preview-vimeo" title="Play Video &quot;' . $data->title . '&quot;">
					
				</div>';
       		return $preview_url;
	    }

	    else return $return;
	}

	function lazyload_create_menu() {
		add_options_page('Lazy Load for Videos', 'Lazy Load for Videos', 'manage_options', 'lazyload.php', array( $this, 'lazyload_settings_page' ));
	}

	function register_lazyload_settings() {
		$arr = array(
			// Youtube
			'lly_opt',
			'lly_opt_title',
			'lly_opt_support_for_widgets',
			'lly_opt_player_colour',
			'lly_opt_player_relations',
			'lly_opt_player_controls',
			'll_opt_thumbnail_size',

			// Vimeo
			'llv_opt',
			'llv_opt_title',

			//Other
			'll_opt_customcss'
		);

		foreach ( $arr as $i ) {
			register_setting( 'll-settings-group', $i );
		}
	}

	function lazyload_settings_page()	{ ?>

		<div id="tabs" class="ui-tabs">
			<h2>Lazy Load for Videos <span class="subtitle">by <a href="http://kevinw.de/ll" target="_blank" title="Website by Kevin Weber">Kevin Weber</a> (Version <?php echo LL_VERSION; ?>)</span></h2>

			<ul class="ui-tabs-nav">
		        <li><a href="#tabs-1">Youtube</a></li>
		    	<li><a href="#tabs-2">Vimeo</a></li>
		        <li><a href="#tabs-3">Styling/Other</a></li>
		    </ul>
			
			<form method="post" action="options.php">
			<?php
			    settings_fields( 'll-settings-group' );
		   		do_settings_sections( 'll-settings-group' );
		   	?>

				<div id="tabs-1">

					<h3>Lazy Load for Youtube</h3>

					<table class="form-table">
						<tbody>
					        <tr valign="top">
						        <th scope="row"><label>Do NOT use Lazy Load for Youtube</label></th>
						        <td>
									<input name="lly_opt" type="checkbox" value="1" <?php checked( '1', get_option( 'lly_opt' ) ); ?> /> <label>If checked, Lazy Load will not be used for <b>Youtube</b> videos.</label>
						        </td>
					        </tr>
					        <tr valign="top">
						        <th scope="row"><label>Display Youtube title <span class="newred">New!</span></label></th>
						        <td>
									<input name="lly_opt_title" type="checkbox" value="1" <?php checked( '1', get_option( 'lly_opt_title' ) ); ?> /> <label>If checked, the Youtube video title will be displayed on preview image.</label>
						        </td>
					        </tr>
					        <tr valign="top">
					        	<th scope="row"><label>Player colour <span class="newred">New!</span></label></th>
						        <td>
									<select class="select" typle="select" name="lly_opt_player_colour">
										<option value="dark"<?php if (get_option('lly_opt_player_colour') === 'dark') { echo ' selected="selected"'; } ?>>Dark (standard)</option>
										<option value="light"<?php if (get_option('lly_opt_player_colour') === 'light') { echo ' selected="selected"'; } ?>>Light</option>
									</select>
						        </td>
					        </tr>
					        <tr valign="top">
					        	<th scope="row"><label>Don't display related videos <span class="newred">New!</span></label></th>
						        <td>
									<input name="lly_opt_player_relations" type="checkbox" value="1" <?php checked( '1', get_option( 'lly_opt_player_relations' ) ); ?> /> <label>If checked, related videos at the end of your videos will not be displayed.</label>
						        </td>
					        </tr>
					        <tr valign="top">
					        	<th scope="row"><label>Don't dipslay player controls <span class="newred">New!</span></label></th>
						        <td>
									<input name="lly_opt_player_controls" type="checkbox" value="1" <?php checked( '1', get_option( 'lly_opt_player_controls' ) ); ?> /> <label>If checked, Youtube player controls will not be displayed.</label>
						        </td>
					        </tr>
					        <tr valign="top">
						        <th scope="row"><label>Support for widgets <span class="newred">New!</span></label></th>
						        <td>
									<input name="lly_opt_support_for_widgets" type="checkbox" value="1" <?php checked( '1', get_option( 'lly_opt_support_for_widgets' ) ); ?> /> <label>If checked, you can paste a Youtube URL into a text widget and it will be lazy loaded.</label>
						        </td>
					        </tr>
					        <p class="notice"><span style="color:#f60;">Important:</span> Enabling/disabling Lazy Load for Vimeo will only affect new posts and posts you update afterwards. (Open the post editor and update/save your post again.)</p>
			        	</tbody>
		        	</table>
		        </div>


				<div id="tabs-2">

					<h3>Lazy Load for Vimeo</h3>

					<table class="form-table">
						<tbody>
					        <tr valign="top">
						        <th scope="row"><label>Do NOT use Lazy Load for Vimeo</label></th>
						        <td>
									<input name="llv_opt" type="checkbox" value="1" <?php checked( '1', get_option( 'llv_opt' ) ); ?> /> <label>If checked, Lazy Load will not be used for <b>Vimeo</b> videos.</label>
						        </td>
					        </tr>
					        <tr valign="top">
						        <th scope="row"><label>Display Vimeo title <span class="newred">New!</span></label></th>
						        <td>
									<input name="llv_opt_title" type="checkbox" value="1" <?php checked( '1', get_option( 'llv_opt_title' ) ); ?> /> <label>If checked, the Vimeo video title will be displayed on preview image.</label>
						        </td>
					        </tr>
					        <p class="notice"><span style="color:#f60;">Important:</span> Enabling/disabling Lazy Load for Youtube will only affect new posts and posts you update afterwards. (Open the post editor and update/save your post again.)</p>
			        	</tbody>
		        	</table>
		        </div>


				<div id="tabs-3">

					<h3>Styling/Other</h3>

					<table class="form-table">
						<tbody>
					        <tr valign="top">
					        	<th scope="row"><label>Thumbnail Size <span class="newred">New!</span></label></th>
						        <td>
									<select class="select" typle="select" name="ll_opt_thumbnail_size">
										<option value="standard"<?php if (get_option('ll_opt_thumbnail_size') === 'standard') { echo ' selected="selected"'; } ?>>Standard</option>
										<option value="cover"<?php if (get_option('ll_opt_thumbnail_size') === 'cover') { echo ' selected="selected"'; } ?>>Cover</option>
									</select>
						        </td>
					        </tr>
					        <tr valign="top">
					        	<th scope="row"><label>Custom CSS</label></th>
					        	<td>
					        		<textarea rows="14" cols="70" type="text" name="ll_opt_customcss"><?php echo get_option('ll_opt_customcss'); ?></textarea>
					        	</td>
					        </tr>
					    </tbody>
				    </table>

			    </div>


			    <?php submit_button(); ?>
			</form>

			<?php require_once( 'inc/signup.php' ); ?>

		    <table class="form-table">
		        <tr valign="top">
			        <th scope="row" style="width:100px;"><a href="http://kevinw.de/ll" target="_blank"><img src="http://www.gravatar.com/avatar/9d876cfd1fed468f71c84d26ca0e9e33?d=http%3A%2F%2F1.gravatar.com%2Favatar%2Fad516503a11cd5ca435acc9bb6523536&s=100" style="-webkit-border-radius:50%;-moz-border-radius:50%;border-radius:50%;"></a></th>
			        <td style="width:200px;">
			        	<p><a href="http://kevinw.de/ll" target="_blank">Kevin Weber</a> &ndash; that's me.<br>
			        	I'm the developer of this plugin. I hope you enjoy it!</p>
			        </td>
			        <td>
						<p><b>It's free!</b> Support me with <a href="http://kevinw.de/donate/LazyLoadVideos/" title="Pay him something to eat" target="_blank">a delicious lunch</a> and give this plugin a 5 star rating <a href="http://wordpress.org/support/view/plugin-reviews/lazy-load-for-videos?filter=5" title="Vote for Lazy Load for Videos" target="_blank">on WordPress.org</a>.</p>
			        </td>
			        <td>
						<p>Another great plugin: <a href="http://kevinw.de/ll-ind" title="Inline Comments" target="_blank">Inline Comments</a>.</p>
			        </td>
		        </tr>
		    </table>
		</div>
	<?php
	}

	function lazyload_admin_js() {
	    wp_enqueue_script( 'lazyload_admin_js', plugins_url( '../js/min/admin-ck.js' , __FILE__ ), array('jquery', 'jquery-ui-tabs') );
	}

	function lazyload_admin_css() {
		wp_enqueue_style( 'lazyload_admin_css', plugins_url('../css/min/admin.css', __FILE__) );
	}

}

function initialize_lazyload_admin() {
	$lazyload_admin = new LAZYLOAD_Admin();
}
add_action( 'init', 'initialize_lazyload_admin' );
?>