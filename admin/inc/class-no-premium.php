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
		echo '<li><a href="#tab-premium" class="tab-orange tab-premium">Premium</a></li>';
	}
	// Step 2
	function add_admin_tab() { ?>
		<div id="tab-premium">

			<h3><?php esc_html_e( 'Premium Extension', LL_TD ); ?></h3>

			<table class="form-table">
				<tbody>
					<tr valign="top">
						<th><?php esc_html_e( 'It\'s pretty simple:', LL_TD ); ?></th>
						<td>
							<p><?php esc_html_e( 'I offer nearly every feature of Lazy Load for Videos for free. So you can ensure that everything works fine on your site before you grab this slick extension.', LL_TD ); ?></p>
							<p><?php esc_html_e( 'This extension removes the subtle "i" (information link) that is placed in the top right of every video. That\'s it.', LL_TD ); ?></p>
							<p style="color:#999;"><i><?php esc_html_e( 'Using this link I assure that the plugin gets spread. And everyone who doesn’t want the branding pays a very little compensation for my time-consuming efforts.', LL_TD ); ?></i></p>
						</td>
					</tr>
			        <tr valign="top">
			        	<th scope="row"><?php esc_html_e( 'Remove branding', LL_TD ); ?></th>
				        <td>
							<span><?php esc_html_e( 'This extension removes the subtle "i" (information link) from your videos automatically. Simply install and activate both plugins (basic plugin and premium extension).', LL_TD ); ?></span>
				        </td>
			        </tr>
			        <tr valign="top">
			        	<th scope="row"><?php esc_html_e( 'Lifetime updates', LL_TD ); ?><br><span class="description thin"><?php esc_html_e( 'Enjoy all coming features!', LL_TD ); ?></span></th>
				        <td>
							<span><?php esc_html_e( 'No matter what comes next: Once you\'ve bought premium, you\'re going to get every new feature for free.', LL_TD ); ?></span>
				        </td>
			        </tr>
			        <tr valign="top">
			        	<th scope="row"><a href="https://sellfy.com/p/sFX6/" id="sFX6" class="sellfy-buy-button">buy</a><script type="text/javascript" src="https://sellfy.com/js/api_buttons.js"></script></th>
				        <td>
							<span><?php esc_html_e( 'Grab premium to get additional features, honour my work and push development. The price might change/increase over time.', LL_TD ); ?><br>
							<strong><?php esc_html_e( 'Immediate download after purchase.', LL_TD ); ?></strong>
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