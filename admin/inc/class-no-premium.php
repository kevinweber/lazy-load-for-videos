<?php
/**
 * @package Admin
 */

class Lazyload_Videos_No_Premium_Admin_Options {

	function __construct() {
		$this->register_no_premium_settings();
	}

	/**
	 * Add content and settings to options page
	 */
	function register_no_premium_settings() {
		add_filter( 'lazyload_settings_page_tabs_link_after', array( $this, 'add_admin_tab_link' ) );
		add_filter( 'lazyload_settings_page_tabs_after', array( $this, 'add_admin_tab' ) );
	}

	// Step 1
	function add_admin_tab_link() {
		echo '<li><a href="#tab-no-premium" class="tab-orange tab-premium">Premium <span class="newred_dot">&bull;</span></a></li>';
	}
	// Step 2
	function add_admin_tab() { ?>
		<div id="tab-no-premium">

			<h3>Get Premium and &hellip;</h3>

			<table class="form-table">
				<tbody>

			        <tr valign="top">
			        	<th scope="row">&hellip; remove branding</th>
				        <td>
							<span>The <i>Premium Extension</i> automatically removes the branding link from your videos.</span>
				        </td>
			        </tr>
			        <tr valign="top">
			        	<th scope="row">&hellip; pre-roll/post-roll ads <span class="newred">New!</span><span class="description thin"><br>Sell advertising space!</span></th>
			        	<td>
			        		<label>Convert all Youtube videos into a playlist and automatically add your corporate video, product teaser or another video advertisement. You have to insert the plain Youtube <b>video ID</b>, like <b>Dp2mI9AgiGs</b> or a comma-separated list of video IDs (<i>Dp2mI9AgiGs,IJNR2EpS0jw</i>).</label><br><br><label>&raquo;I'm very proud of this feature because it gives you a new space to promote your brand or sell advertisements! An advertiser might pay to play his video before your actual video starts to play. Isn't this an amazing opportunity?&laquo;<br>&ndash; <a href="http://kevinw.de/ll" target="_blank">Kevin Weber</a>, digital marketer and developer of this plugin</label>
			        	</td>
			        </tr>
			        <tr valign="top">
				        <th scope="row"><label>&hellip; apply schema.org markup <span class="newred">Beta</span></label></th>
				        <td>
							<label>Add schema.org markup to your Youtube and Vimeo videos. Those changes don't seem to affect your search ranking because videos and schema.org markup <a href="https://developers.google.com/webmasters/videosearch/schema" target="_blank">should be visible</a> without JavaScript (but that cannot be the case when videos are lazy loaded).</label>
				        </td>
			        </tr>
			        <tr valign="top">
			        	<th scope="row">&hellip; enjoy coming features<br><span class="description thin">with free lifetime updates!</span></th>
				        <td>
							<span>Get all future updates of this premium extension for free!</span>
				        </td>
			        </tr>
			        <tr valign="top">
			        	<th scope="row"><a href="https://sellfy.com/p/sFX6/" id="sFX6" class="sellfy-buy-button">buy</a><script type="text/javascript" src="https://sellfy.com/js/api_buttons.js"></script></th>
				        <td>
							<span>Buy premium to get additional features, honour my work and push development. The price might change/increase over time.<br>
							<strong>Immediate download after purchase.</strong>
				        </td>
			        </tr>
			    </tbody>
		    </table>

	    </div>
	<?php }

}

function initialize_lazyload_videos_no_premium_admin_options() {
	new Lazyload_Videos_No_Premium_Admin_Options();
}
add_action( 'init', 'initialize_lazyload_videos_no_premium_admin_options' );
?>