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
		if ( isset( $_GET['page'] ) && ( $_GET['page'] == LL_ADMIN_URL ) ) {
			if ( isset( $_GET['update_posts'] ) && $_GET['update_posts'] == 'with_oembed' ) {
				lazyload_update_posts_with_embed();
			}
			$this->lazyload_admin_css();
			$this->lazyload_admin_js();
		}
		$plugin = plugin_basename( LL_FILE ); 
		add_filter("plugin_action_links_$plugin", array( $this, 'lazyload_settings_link' ) );
		$this->register_lazyload_settings();
	}

	/**
	 * Add settings link on plugin page
	 */
	function lazyload_settings_link($links) { 
	  $settings_link = '<a href="options-general.php?page='. LL_ADMIN_URL .'">Settings</a>'; 
	  array_unshift($links, $settings_link); 
	  return $links; 
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

	    	$a_class = 'lazy-load-youtube preview-youtube';
	    	$a_class = apply_filters( 'lazyload_preview_url_a_class_youtube', $a_class );

       		$preview_url = '<a class="' . $a_class . '" href="' . $url . '" video-title="' . $data->title . '" title="Play Video &quot;' . $data->title . '&quot;">&ensp;</a>';
       		return apply_filters( 'lazyload_replace_video_preview_url_youtube', $preview_url );
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

	    	$a_class = 'lazy-load-vimeo preview-vimeo';
	    	$a_class = apply_filters( 'lazyload_preview_url_a_class_youtube', $a_class );

			$preview_url = '<div id="' . $vimeoid . '" class="' . $a_class . '" title="Play Video &quot;' . $data->title . '&quot;">
					
				</div>';
			return apply_filters( 'lazyload_replace_video_preview_url_vimeo', $preview_url );
	    }

	    else return $return;
	}

	function lazyload_create_menu() {
		add_options_page('Lazy Load for Videos', 'Lazy Load for Videos', 'manage_options', LL_ADMIN_URL, array( $this, 'lazyload_settings_page' ));
	}

	function register_lazyload_settings() {
		$arr = array(
			// Youtube
			'lly_opt',
			'lly_opt_title',
			'lly_opt_support_for_widgets',
			'lly_opt_player_colour',
			'lly_opt_player_colour_progress',
			'lly_opt_player_relations',
			'lly_opt_player_controls',
			'lly_opt_player_playlist',
			'll_opt_thumbnail_size',

			// Vimeo
			'llv_opt',
			'llv_opt_title',
			'llv_opt_player_colour',

			//Other
			'll_opt_customcss',
			'll_opt_support_for_tablepress'
		);

		foreach ( $arr as $i ) {
			register_setting( 'll-settings-group', $i );
		}
		do_action( 'lazyload_register_settings_after' );
	}

	function lazyload_settings_page()	{ ?>

		<div id="tabs" class="ui-tabs">
			<h2>Lazy Load for Videos <span class="subtitle">by <a href="http://kevinw.de/ll" target="_blank" title="Website by Kevin Weber">Kevin Weber</a> (Version <?php echo LL_VERSION; ?>)</span></h2>

			<ul class="ui-tabs-nav">
		        <li><a href="#tab-youtube">Youtube</a></li>
		    	<li><a href="#tab-vimeo">Vimeo</a></li>
		        <li><a href="#tab-other">General/Styling/Other</a></li>
		        <?php do_action( 'lazyload_settings_page_tabs_link_after' ); ?>
		    </ul>
			
			<form method="post" action="options.php">
			<?php
			    settings_fields( 'll-settings-group' );
		   		do_settings_sections( 'll-settings-group' );
		   	?>

				<div id="tab-youtube">

					<h3>Lazy Load for Youtube</h3>

					<table class="form-table">
						<tbody>
					        <tr valign="top">
						        <th scope="row"><label><u>Do NOT use Lazy Load for Youtube</u></label></th>
						        <td>
									<input name="lly_opt" type="checkbox" value="1" <?php checked( '1', get_option( 'lly_opt' ) ); ?> /> <label>If checked, Lazy Load will not be used for <b>Youtube</b> videos.</label>
						        </td>
					        </tr>
					        <tr valign="top">
						        <th scope="row"><label>Display Youtube title</label></th>
						        <td>
									<input name="lly_opt_title" type="checkbox" value="1" <?php checked( '1', get_option( 'lly_opt_title' ) ); ?> /> <label>If checked, the Youtube video title will be displayed on preview image.</label>
						        </td>
					        </tr>
					        <tr valign="top">
					        	<th scope="row"><label>Player colour</label></th>
						        <td>
									<select class="select" typle="select" name="lly_opt_player_colour">
										<option value="dark"<?php if (get_option('lly_opt_player_colour') === 'dark') { echo ' selected="selected"'; } ?>>Dark (default)</option>
										<option value="light"<?php if (get_option('lly_opt_player_colour') === 'light') { echo ' selected="selected"'; } ?>>Light</option>
									</select>
						        </td>
					        </tr>
					        <tr valign="top">
					        	<th scope="row"><label>Colour of progress bar</label></th>
						        <td>
									<select class="select" typle="select" name="lly_opt_player_colour_progress">
										<option value="red"<?php if (get_option('lly_opt_player_colour_progress') === 'red') { echo ' selected="selected"'; } ?>>Red (default)</option>
										<option value="white"<?php if (get_option('lly_opt_player_colour_progress') === 'white') { echo ' selected="selected"'; } ?>>White</option>
									</select>
						        </td>
					        </tr>
					        <tr valign="top">
					        	<th scope="row"><label>Hide related videos</label></th>
						        <td>
									<input name="lly_opt_player_relations" type="checkbox" value="1" <?php checked( '1', get_option( 'lly_opt_player_relations' ) ); ?> /> <label>If checked, related videos at the end of your videos will not be displayed.</label>
						        </td>
					        </tr>
					        <tr valign="top">
					        	<th scope="row"><label>Hide player controls</label></th>
						        <td>
									<input name="lly_opt_player_controls" type="checkbox" value="1" <?php checked( '1', get_option( 'lly_opt_player_controls' ) ); ?> /> <label>If checked, Youtube player controls will not be displayed.</label>
						        </td>
					        </tr>
					        <tr valign="top">
					        	<th scope="row"><label>Playlist (branding, video ads) <span class="newred">New!</span><span class="description thin"><br>&hellip; sell advertising space!</label></th>
					        	<td>
					        		<input type="text" name="lly_opt_player_playlist" placeholder="" value="<?php echo get_option('lly_opt_player_playlist'); ?>" /><br><label>Convert all videos into a playlist and automatically add your corporate video, product teaser or another video advertisment at the end of every video. You have to insert the plain <b>video ID</b>, like <b>Dp2mI9AgiGs</b> or a comma-separated list of video IDs (<i>Dp2mI9AgiGs,IJNR2EpS0jw</i>).</label><br><br><label>&raquo;I'm very proud of this feature because it gives you a new space to promote your brand or sell advertisements! An advertiser might pay to play his video following every video on your site. Isn't this an amazing opportunity?&laquo;<br>&ndash; <a href="http://kevinw.de/ll" target="_blank">Kevin Weber</a>, digital marketer and developer of this plugin</label>
					        	</td>
					        </tr>
					        <tr valign="top">
						        <th scope="row"><label>Support for widgets</label></th>
						        <td>
									<input name="lly_opt_support_for_widgets" type="checkbox" value="1" <?php checked( '1', get_option( 'lly_opt_support_for_widgets' ) ); ?> /> <label>Only check this box if you actually use this feature (for reason of performance)! If checked, you can paste a Youtube URL into a text widget and it will be lazy loaded.</label>
						        </td>
					        </tr>
					        <?php echo LL_NOTICE; ?>
			        	</tbody>
		        	</table>
		        </div>

				<div id="tab-vimeo">

					<h3>Lazy Load for Vimeo</h3>

					<table class="form-table">
						<tbody>
					        <tr valign="top">
						        <th scope="row"><label><u>Do NOT use Lazy Load for Vimeo</u></label></th>
						        <td>
									<input name="llv_opt" type="checkbox" value="1" <?php checked( '1', get_option( 'llv_opt' ) ); ?> /> <label>If checked, Lazy Load will not be used for <b>Vimeo</b> videos.</label>
						        </td>
					        </tr>
					        <tr valign="top">
						        <th scope="row"><label>Display Vimeo title</label></th>
						        <td>
									<input name="llv_opt_title" type="checkbox" value="1" <?php checked( '1', get_option( 'llv_opt_title' ) ); ?> /> <label>If checked, the Vimeo video title will be displayed on preview image.</label>
						        </td>
					        </tr>
					        <tr valign="top">
					        	<th scope="row"><label>Colour of the video controls <span class="newred">New!</span></label></th>
					        	<td>
					        		<input id="llv_picker_input_player_colour" class="picker-input" type="text" name="llv_opt_player_colour" placeholder="#00adef" value="<?php if (get_option("llv_opt_player_colour") == "") { echo "#00adef"; } else { echo get_option("llv_opt_player_colour"); } ?>" />
					        		<div id="llv_picker_player_colour" class="picker-style"></div>
					        	</td>
					        </tr>
					       	<?php echo LL_NOTICE; ?>
			        	</tbody>
		        	</table>
		        </div>


				<div id="tab-other">

					<h3>General/Styling/Other</h3>

					<table class="form-table">
						<tbody>
					        <tr valign="top">
					        	<th scope="row"><label>Thumbnail Size</label></th>
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
					        <tr valign="top">
						        <th scope="row"><label>Support for TablePress <span class="newred">New!</span></label></th>
						        <td>
									<input name="ll_opt_support_for_tablepress" type="checkbox" value="1" <?php checked( '1', get_option( 'll_opt_support_for_tablepress' ) ); ?> /> <label>Only check this box if you actually use this feature (for reason of performance)! If checked, you can paste a Youtube or Vimeo URL into tables that are created with TablePress and it will be lazy loaded.</label>
						        </td>
					        </tr>
					    </tbody>
				    </table>

			    </div>

				<?php do_action( 'lazyload_settings_page_tabs_after' ); ?>

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
						<p>Another great plugin: <a href="http://kevinw.de/ll-ind" title="Inline Comments" target="_blank">Inline Comments</a>.</p>
			        </td>
			        <td>
						<p><b>It's free!</b> Support me with <a href="http://kevinw.de/donate/LazyLoadVideos/" title="Pay him something to eat" target="_blank">a delicious lunch</a> and give this plugin a 5 star rating <a href="http://wordpress.org/support/view/plugin-reviews/lazy-load-for-videos?filter=5" title="Vote for Lazy Load for Videos" target="_blank">on WordPress.org</a>.</p>
			        </td>
		        </tr>
		    </table>
		</div>
	<?php
	}

	function lazyload_admin_js() {
	    wp_enqueue_script( 'lazyload_admin_js', plugins_url( '../js/min/admin-ck.js' , __FILE__ ), array('jquery', 'jquery-ui-tabs', 'farbtastic' ) );
	}

	function lazyload_admin_css() {
		wp_enqueue_style( 'lazyload_admin_css', plugins_url('../css/min/admin.css', __FILE__) );
		wp_enqueue_style( 'farbtastic' );	// Required for colour picker
	}

}

function initialize_lazyload_admin() {
	$lazyload_admin = new LAZYLOAD_Admin();
}
add_action( 'init', 'initialize_lazyload_admin' );
?>