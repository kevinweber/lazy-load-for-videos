<?php
/**
 * @package Admin
 */

class LAZYLOAD_No_Premium_Admin_Options {

	function __construct() {
		$this->register_no_premium_settings();
	}

	/**
	 * Add content and settings to options page
	 */
	function register_no_premium_settings() {
		add_filter( 'lazyload_register_settings_after', array( $this, 'register_no_premium_settings_after' ) );
		add_filter( 'lazyload_settings_page_tabs_link_after', array( $this, 'add_admin_tab_link' ) );
		add_filter( 'lazyload_settings_page_tabs_after', array( $this, 'add_admin_tab' ) );
	}
	// Step 1
	function register_no_premium_settings_after() {
		$arr = array(
			'll_remove_branding',
			'lly_opt_player_playlist',
		);
		foreach ( $arr as $i ) {
			register_setting( 'll-settings-group', $i );
		}
	}
	// Step 2
	function add_admin_tab_link() {
		echo '<li><a href="#tab-no-premium" class="tab-green tab-premium">FREE Premium <span class="newred_dot">&bull;</span></a></li>';
	}
	// Step 3
	function add_admin_tab() { ?>
		<div id="tab-no-premium">

			<h3>Premium features &ndash; for free</h3>

			<table class="form-table">
				<tbody>
			        <tr valign="top">
				        <th scope="row"><label>Remove Branding <span class="newred">New!</span></label></th>
				        <td>
							<input name="ll_remove_branding" type="checkbox" value="1" <?php checked( '1', get_option( 'll_remove_branding' ) ); ?> /> <label>Remove the info link ("i") that is displayed on every video.</label>
				        </td>
			        </tr>
			        <tr valign="top">
			        	<th scope="row"><label>Playlist (branding, video ads) <span class="newred">New!</span><span class="description thin"><br>&hellip; sell advertising space!</label></th>
			        	<td>
			        		<input type="text" name="lly_opt_player_playlist" placeholder="" value="<?php echo get_option('lly_opt_player_playlist'); ?>" /><br><label>Convert all Youtube videos into a playlist and automatically add your corporate video, product teaser or another video advertisement at the end of every Youtube video. You have to insert the plain Youtube <b>video ID</b>, like <b>Dp2mI9AgiGs</b> or a comma-separated list of video IDs (<i>Dp2mI9AgiGs,IJNR2EpS0jw</i>).</label><br><br><label>&raquo;I'm very proud of this feature because it gives you a new space to promote your brand or sell advertisements! An advertiser might pay to play his video following every video on your site. Isn't this an amazing opportunity?&laquo;<br>&ndash; <a href="http://kevinw.de/ll" target="_blank">Kevin Weber</a>, digital marketer and developer of this plugin</label>
			        	</td>
			        </tr>
			        <tr valign="top">
			        	<th scope="row"><label>More to come.</label></th>
				        <td>
							<span>Wait&hellip; why are the above features available for free? Because I want to say <b>THANK YOU for more than 10.000 downloads</b> of this plugin! I plan to improve support for well-known plugins like BuddyPress and even want to lazy load other formats, like SoundCloud, SlideShare and Spotify.</span>
				        </td>
			        </tr>
			    </tbody>
		    </table>

	    </div>
	<?php }

}

function initialize_lazyload_no_premium_admin_options() {
	$lazyload_no_premium_admin_options = new LAZYLOAD_No_Premium_Admin_Options();
}
add_action( 'init', 'initialize_lazyload_no_premium_admin_options' );
?>