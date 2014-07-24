<?php
/**
 * @package Admin
 */

class INCOM_No_Premium_Admin_Options {

	function __construct() {
		$this->register_incom_no_premium_settings();
	}

	/**
	 * Add content and settings to options page
	 */
	function register_incom_no_premium_settings() {
		add_filter( 'register_incom_settings_after', array( $this, 'register_incom_no_premium_settings_after' ) );
		add_filter( 'incom_settings_page_tabs_link_after', array( $this, 'add_incom_admin_tab_link' ) );
		add_filter( 'incom_settings_page_tabs_after', array( $this, 'add_incom_admin_tab' ) );
	}
	// Step 1
	function register_incom_no_premium_settings_after() {
		$arr = array(
			'displayBranding',
			'displayAvatars'
		);
		foreach ( $arr as $i ) {
			register_setting( 'incom-settings-group', $i );
		}
	}
	// Step 2
	function add_incom_admin_tab_link() {
		echo '<li><a href="#tab-no-premium" class="tab-orange tab-premium">Premium</a></li>';
	}
	// Step 3
	function add_incom_admin_tab() { ?>
		<div id="tab-no-premium">

			<h3>Get Premium and &hellip;</h3>

			<table class="form-table">
				<tbody>
			        <tr valign="top">
			        	<th scope="row">&hellip; remove branding</th>
				        <td>
							<span>The <i>Premium Extension</i> automatically removes the branding link from Inline Comments.</span>
				        </td>
			        </tr>
			        <tr valign="top">
			        	<th scope="row">&hellip; display avatars<br><span class="description thin">next to each comment</span></th>
				        <td>
							<span>Display photos/avatars from commentators next to each comment.</span>
				        </td>
			        </tr>
			        <tr valign="top">
			        	<th scope="row">&hellip; get preferred support<br><span class="description thin">to setup and style Inline Comments</span></th>
				        <td>
							<span>I help you to choose the correct selectors and assist you to make Inline Comments good-looking on your site.</span>
				        </td>
			        </tr>
			        <tr valign="top">
			        	<th scope="row">&hellip; enjoy coming features</th>
				        <td>
							<span>Here is so much more to come!<br>What do you think of social logins (Twitter, Facebook) and the possibility to reply to specific inline comments?</span>
				        </td>
			        </tr>
			        <tr valign="top">
			        	<th scope="row"><span style="color:#f90;font-size:1.25em;font-weight:normal">44€</span><br><span class="description thin">starting in August</span></th>
				        <td>
							<span><b>55% introductory offer</b>: The first <b>55 people who contact me</b> in July get a lifetime license for merely <span style="color:#009000;font-size:1.25em"><b>19,80€</b></span>! <a href="http://kevinw.de/kontakt" target="_blank">Click here to contact me right now</a>.</span>
				        </td>
			        </tr>
			    </tbody>
		    </table>

	    </div>
	<?php }

}

function initialize_incom_no_premium_admin_options() {
	$incom_no_premium_admin_options = new INCOM_No_Premium_Admin_Options();
}
add_action( 'init', 'initialize_incom_no_premium_admin_options' );
?>