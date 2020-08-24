<?php
class Lazy_Load_For_Videos_Theme_Check {

	/**
	 * Test if wp_footer function exists in user's theme
	 * Based on a Gist by Matt Martz (http://gist.github.com/378450)
	 */
	function theme_check_init( $on_specific_page = 'everywhere' ) {
		if ( isset( $_GET['page'] ) && ( $_GET['page'] == 'lazyload.php') || $on_specific_page == 'everywhere'  ) {
			// Hook in at admin_init to perform the check for wp_footer
			add_action( 'admin_init', array( $this, 'theme_check_footer' ) );
		}
		// If test-footer query var exists hook into wp_footer
		if ( isset( $_GET['test-footer'] ) )
			add_action( 'wp_footer', array( $this, 'theme_check_test_footer' ), 99999 ); // Some obscene priority, make sure we run last
	}
	// Echo a string that we can search for later into the footer of the document
	// This should end up appearing directly before </body>
	function theme_check_test_footer() {
		echo '<!--wp_footer-->';
	}
	// Check for the existence of the strings where wp_footer should have been called from
	function theme_check_footer() {
		// Build the url to call, NOTE: uses home_url and thus requires WordPress 3.0
		$url = add_query_arg( array( 'test-footer' => '' ), home_url() );
		// Perform the HTTP GET ignoring SSL errors
		$response = wp_remote_get( $url, array( 'sslverify' => false ) );
		// Grab the response code and make sure the request was sucessful
		$code = (int) wp_remote_retrieve_response_code( $response );
		if ( $code == 200 ) {
			// Strip all tabs, line feeds, carriage returns and spaces
			$html = preg_replace( '/[\t\r\n\s]/', '', wp_remote_retrieve_body( $response ) );
			// Check to see if we found the existence of wp_footer
			if ( ! strstr( $html, '<!--wp_footer-->' ) )
				add_action ( 'admin_notices', array( $this, 'theme_check_test_footer_notices' ) );
			// If we found errors with the existence of wp_footer hook into admin_notices to complain about it		
		}
	}
	// Output the notice
	function theme_check_test_footer_notices() {
		echo '<div class="error"><p>Your active theme might be <strong>missing</strong> the call to &lt;?php wp_footer(); ?&gt;<br>Always have it just before the closing </body> tag of your theme, or you will break many plugins. See <a href="https://codex.wordpress.org/Function_Reference/wp_footer" target="_blank">wordpress.org</a>.</p></div>';
	}
	
}
?>