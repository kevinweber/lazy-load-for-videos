<?php
/**
 * Create options panel (https://codex.wordpress.org/Creating_Options_Pages)
 */
class Lazy_Load_For_Videos_Admin {
	function __construct() {
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		// The 'oembed_dataparse' filter should be called on backend AND on frontend, not only on backend [is_admin()]. Otherwise, on some websites occur errors.
		add_filter( 'oembed_dataparse', array( $this, 'lazyload_replace_video' ), 10, 3 );
		add_action( 'admin_menu', array( $this, 'lazyload_create_menu' ) );
		$this->lazyloadvideos_update_posts_with_embed();
	}

	function admin_enqueue_scripts() {
		if ( isset( $_GET['page'] ) && ( $_GET['page'] == LL_ADMIN_URL ) ) {
			$this->lazyload_admin_css();
			$this->lazyload_admin_js();
		}
	}

	function admin_init() {
		$plugin = plugin_basename( LL_FILE );
		add_filter("plugin_action_links_$plugin", array( $this, 'lazyload_settings_link' ) );
		$this->register_lazyload_settings();
	}

	/*
	 * Update posts with embed when user has clicked "Update Posts"
	 * @info: lazyloadvideos_update_posts_with_embed() is loaded by class-register.php
	 */
	function lazyloadvideos_update_posts_with_embed() {
		if ( isset( $_POST['update_posts'] ) && $_POST['update_posts'] == 'with_oembed' ) {
			Lazy_Load_For_Videos_Update_Posts::delete_oembed_caches();
		}
	}

	/**
	 * Add settings link on plugin page
	 */
	function lazyload_settings_link($links) {
	  $settings_link = '<a href="options-general.php?page='. LL_ADMIN_URL .'">'.esc_html__( 'Settings', LL_TD ).'</a>';
	  array_unshift($links, $settings_link);
	  return $links;
	}

    function text__no_script_fallback($title, $url) {
        $no_script_fallback = "<noscript>Video can't be loaded because JavaScript is disabled: <a href=\"{$url}\" title=\"{$title}\">{$title} ({$url})</a></noscript>";

        return $no_script_fallback;
    }

	/**
	 * Replace embedded Youtube and Vimeo videos with a special piece of code.
	 * Thanks to Otto's comment on StackExchange (See https://wordpress.stackexchange.com/a/19533)
	 */
	function lazyload_replace_video($return, $data, $url) {
		global $lazyload_videos_general;

		// If URL contains "lazyload=0", we don't want to lazyload it.
		if (strpos($url, 'lazyload=0') !== false) {
	    	return $return;
		}

		// Youtube support
	    if ( (! is_feed()) && ($data->provider_name == 'YouTube')
				&& (get_option('lly_opt') == false) // test if Lazy Load for Youtube is deactivated
	    	) {

	    	$a_class = 'lazy-load-youtube preview-lazyload preview-youtube';
	    	$a_class = apply_filters( 'lazyload_preview_url_css_youtube', $a_class );

			$play_title_text = sprintf(esc_attr__( 'Play video &quot;%s&quot;', LL_TD ),
				$data->title
			);

       		$preview_url = "<a class=\"{$a_class}\" href=\"{$url}\" data-video-title=\"{$data->title}\" title=\"{$play_title_text}\">{$url}</a>";

 			// Wrap container around $preview_url
       		$preview_url = '<div class="container-lazyload preview-lazyload container-youtube js-lazyload--not-loaded">'
					. $preview_url
					. $this->text__no_script_fallback($data->title, $url)
					. '</div>';

       		return apply_filters( 'lazyload_replace_video_preview_url_youtube', $preview_url );
	    }

	    // Vimeo support
	    elseif ( (! is_feed()) && ($data->provider_name == 'Vimeo')
				&& (get_option('llv_opt') == false) // test if Lazy Load for Vimeo is deactivated
	    	) {
			$url_path = parse_url($url, PHP_URL_PATH);

			$url_split_path = explode("/", $url_path);
			foreach($url_split_path as $key=>$value)
			{
			    if ( empty( $value ) )
			        unset($url_split_path[$key]);
			};
			$vimeoid = end($url_split_path);

	    	$a_class = 'lazy-load-vimeo preview-lazyload preview-vimeo';
	    	$a_class = apply_filters( 'lazyload_preview_url_css_vimeo', $a_class );

			$play_title_text = sprintf(esc_attr__( 'Play video &quot;%s&quot;', LL_TD ),
				$data->title
			);

			$preview_url = "<a href=\"{$url}\" id=\"{$vimeoid}\" class=\"{$a_class}\" data-video-thumbnail=\"{$data->thumbnail_url}\" data-video-title=\"{$data->title}\" title=\"{$play_title_text}\">{$url}</a>";

			// Wrap container around $preview_url
			$preview_url = '<div class="container-lazyload container-vimeo js-lazyload--not-loaded">'
                    . $preview_url
                    . $this->text__no_script_fallback($data->title, $url)
					. '</div>';

			return apply_filters( 'lazyload_replace_video_preview_url_vimeo', $preview_url );
	    }

	    else return $return;
	}

	function lazyload_create_menu() {
		add_options_page( esc_html__( 'Lazy Load for Videos', LL_TD ), esc_html__( 'Lazy Load for Videos', LL_TD ), 'manage_options', LL_ADMIN_URL, array( $this, 'lazyload_settings_page' ));
	}

	function register_lazyload_settings() {
		$arr = array(
			//General/Styling
			'll_opt_load_scripts',
			'll_opt_button_style',
			'll_opt_thumbnail_size',
			'll_opt_customcss',
			'll_opt_support_for_tablepress',
			'll_attribute',

			// Youtube
			'lly_opt',
			'lly_opt_title',
			'lly_opt_player_preroll',
			'lly_opt_player_postroll',
			'lly_opt_support_for_widgets',
			'lly_opt_thumbnail_quality',
			'lly_opt_player_colour_progress',
			'lly_opt_player_controls',
			'lly_opt_player_loadpolicy',

			// Vimeo
			'llv_opt',
			'llv_opt_title',
			'llv_opt_player_colour',
		);

		foreach ( $arr as $i ) {
			register_setting( 'll-settings-group', $i );
		}
		do_action( 'lazyload_register_settings_after' );
	}

	function lazyload_settings_page()	{ ?>

		<?php if ( isset( $_POST['update_posts'] ) && $_POST['update_posts'] == 'with_oembed' ) { ?>
			<div class="update-posts updated"><p><?php esc_html_e( 'Your posts have been updated successfully.', LL_TD ); ?></p></div>
		<?php } ?>

		<div id="tabs" class="ui-tabs">
			<h2><?php esc_html_e( 'Lazy Load for Videos', LL_TD ); ?> <span class="subtitle"><?php esc_html_e( 'by', LL_TD ); ?> <a href="https://www.kweber.com/ll" target="_blank" title="<?php esc_html_e( 'Website by Kevin Weber', LL_TD ); ?>">Kevin Weber</a> (<?php esc_html_e( 'Version', LL_TD ); ?> <?php echo LL_VERSION; ?>)</span>
				<br><span class="claim" style="font-size:15px;font-style:italic;position:relative;top:-7px;"><?php esc_html_e( 'Speed up your site and customise your video player!', LL_TD ); ?></span>
			</h2>

			<ul class="ui-tabs-nav">
		        <li><a href="#general"><?php esc_html_e( 'General/Styling', LL_TD ); ?></a></li>
		        <li><a href="#youtube"><?php esc_html_e( 'Youtube', LL_TD ); ?><!--<span class="newred_dot">&bull;</span>--></a></li>
		    	<li><a href="#vimeo"><?php esc_html_e( 'Vimeo', LL_TD ); ?></a></li>
		        <?php do_action( 'lazyload_settings_page_tabs_link_after' ); ?>
		    </ul>

			<form method="post" action="options.php">
			<?php
			    settings_fields( 'll-settings-group' );
		   		do_settings_sections( 'll-settings-group' );
		   	?>


				<div id="general">

					<h3><?php esc_html_e( 'General/Styling', LL_TD ); ?></h3>

					<table class="form-table">
						<tbody>
					        <tr valign="top">
						        <th scope="row"><label><?php esc_html_e( 'Only load CSS/JS when needed', LL_TD ); ?><br><span class="description thin"><?php esc_html_e( 'to improve performance', LL_TD ); ?></span></label></th>
						        <td>
									<input name="ll_opt_load_scripts" type="checkbox" value="1" <?php checked( '1', get_option( 'll_opt_load_scripts' ) ); ?> /> <label><?php esc_html_e( 'When this option is checked, some videos might not lazy load if posts with videos are loaded using Ajax.', LL_TD ); ?></label>
						        </td>
					        </tr>
					        <tr valign="top">
					        	<th scope="row"><label><?php esc_html_e( 'Play Button', LL_TD ); ?></label></th>
						        <td>
									<select class="select" typle="select" name="ll_opt_button_style">
										<option value="default"<?php if (get_option('ll_opt_button_style') === 'default') { echo ' selected="selected"'; } ?>><?php esc_html_e( 'White (CSS-only)', LL_TD ); ?></option>
										<option value="css_white_pulse"<?php if (get_option('ll_opt_button_style') === 'css_white_pulse') { echo ' selected="selected"'; } ?>><?php esc_html_e( 'White Pulse (CSS-only)', LL_TD ); ?></option>
										<option value="css_black"<?php if (get_option('ll_opt_button_style') === 'css_black') { echo ' selected="selected"'; } ?>><?php esc_html_e( 'Black (CSS-only)', LL_TD ); ?></option>
										<option value="css_black_pulse"<?php if (get_option('ll_opt_button_style') === 'css_black_pulse') { echo ' selected="selected"'; } ?>><?php esc_html_e( 'Black Pulse (CSS-only)', LL_TD ); ?></option>
										<option value="youtube_button_image"<?php if (get_option('ll_opt_button_style') === 'youtube_button_image') { echo ' selected="selected"'; } ?>><?php esc_html_e( 'Old Youtube button image', LL_TD ); ?></option>
										<option value="youtube_button_image_red"<?php if (get_option('ll_opt_button_style') === 'youtube_button_image_red') { echo ' selected="selected"'; } ?>><?php esc_html_e( 'Red Youtube button image', LL_TD ); ?></option>
									</select>
						        </td>
					        </tr>
					        <tr valign="top">
					        	<th scope="row"><label><?php esc_html_e( 'Thumbnails/Patterns', LL_TD ); ?></label></th>
						        <td>
									<select class="select" typle="select" name="ll_opt_thumbnail_size">
										<option value="cover"<?php if (get_option('ll_opt_thumbnail_size') === 'cover') { echo ' selected="selected"'; } ?>><?php esc_html_e( 'Thumbnail covering the video element', LL_TD ); ?></option>
										<option value="standard"<?php if (get_option('ll_opt_thumbnail_size') === 'standard') { echo ' selected="selected"'; } ?>><?php esc_html_e( 'Thumbnail contained within the video element', LL_TD ); ?></option>
										<option value="pattern-carbon"<?php if (get_option('ll_opt_thumbnail_size') === 'pattern-carbon') { echo ' selected="selected"'; } ?>><?php esc_html_e( 'Pattern: Carbon', LL_TD ); ?></option>
										<option value="pattern-dots"<?php if (get_option('ll_opt_thumbnail_size') === 'pattern-dots') { echo ' selected="selected"'; } ?>><?php esc_html_e( 'Pattern: Dots', LL_TD ); ?></option>
										<option value="pattern-light-s"<?php if (get_option('ll_opt_thumbnail_size') === 'pattern-light-s') { echo ' selected="selected"'; } ?>><?php esc_html_e( 'Pattern: Light Seigaiha', LL_TD ); ?></option>
										<option value="none"<?php if (get_option('ll_opt_thumbnail_size') === 'none') { echo ' selected="selected"'; } ?>><?php esc_html_e( 'None', LL_TD ); ?></option>
									</select>
									<p><?php esc_html_e( 'For a thumbnail to be displayed, a request needs to be made to the server of a video platform. You can display one of the available patterns instead, or nothing at all.', LL_TD ); ?></p>
						        </td>
					        </tr>
					        <tr valign="top">
					        	<th scope="row"><label><?php esc_html_e( 'Custom CSS', LL_TD ); ?></label></th>
					        	<td>
					        		<textarea rows="14" cols="70" type="text" name="ll_opt_customcss"><?php echo get_option('ll_opt_customcss'); ?></textarea>
					        	</td>
					        </tr>
					        <tr valign="top">
						        <th scope="row"><label><?php esc_html_e( 'Support for TablePress', LL_TD ); ?></label></th>
						        <td>
									<input name="ll_opt_support_for_tablepress" type="checkbox" value="1" <?php checked( '1', get_option( 'll_opt_support_for_tablepress' ) ); ?> /> <label><?php esc_html_e( 'Only check this box if you actually use this feature (for reason of performance). If checked, you can paste a Youtube or Vimeo URL into tables that are created with TablePress and it will be lazy loaded.', LL_TD ); ?></label>
						        </td>
					        </tr>
					        <tr valign="top">
						        <th scope="row"><?php esc_html_e( 'Attribution', LL_TD ); ?><br><span class="description thin"><?php esc_html_e( "Are you thankful for this plugin? I've invested months and months of work. This plugin is 100% free and open source.", LL_TD ); ?></span></th>
						        <td>
									<?php $options = get_option( 'll_attribute' ); ?>
									<input class="radio" type="radio" name="ll_attribute" value="none"<?php checked( 'none' == $options || empty($options) ); ?> /> <label for="none"><?php esc_html_e( 'No attribution: "I can not afford to give appropriate credit for this free plugin."', LL_TD ); ?></label><br><br>
									<input class="radio" type="radio" name="ll_attribute" value="donate"<?php checked( 'donate' == $options ); ?> />
									<label for="donate">
										<?php esc_html_e( 'Donation: "I have donated already or will do so soon."', LL_TD ); ?>
										<?php printf( esc_html__( 'Please %1$sdonate now%2$s so I can keep maintaining and improving this plugin.', LL_TD ),
											'<a href="https://www.kweber.com/donate/LazyLoadVideos/" target="_blank">',
											'</a>'
										); ?>
									</label><br>
						        </td>
					        </tr>
					    </tbody>
				    </table>

				</div>

				<div id="youtube">

					<h3><?php esc_html_e( 'Lazy Load for Youtube', LL_TD ); ?></h3>

					<table class="form-table">
						<tbody>
					        <tr valign="top">
						        <th scope="row"><label><?php esc_html_e( 'Disable Lazy Load for Youtube', LL_TD ); ?></label></th>
						        <td>
									<input name="lly_opt" type="checkbox" value="1" <?php checked( '1', get_option( 'lly_opt' ) ); ?> />
									<label>
										<?php printf( esc_html__( 'If checked, Lazy Load will not be used for %1$s videos.', LL_TD ),
											'<b>Youtube</b>'
										); ?>
									</label>
									<label><span style="color:#f60;"><?php esc_html_e( 'Important:', LL_TD ); ?></span> <?php esc_html_e( 'Updates on this option will only affect new posts and posts you update afterwards with the "Update Posts" button at the bottom of this form.', LL_TD ); ?></label>
						        </td>
					        </tr>
					        <tr valign="top">
						        <th scope="row"><label><?php esc_html_e( 'Display Youtube title', LL_TD ); ?></label></th>
						        <td>
									<input name="lly_opt_title" type="checkbox" value="1" <?php checked( '1', get_option( 'lly_opt_title' ) ); ?> /> <label><?php esc_html_e( 'If checked, the Youtube video title will be displayed on preview image.', LL_TD ); ?></label>
						        </td>
					        </tr>
					        <tr valign="top">
					        	<th scope="row"><label><?php esc_html_e( 'Pre-roll/post-roll ads', LL_TD ); ?><span class="description thin"><br>Sell advertising space!</span></label></th>
					        	<td>
					        		<strong style="width:80px;display:inline-block"><?php esc_html_e( 'Pre-roll', LL_TD ); ?></strong> <input pattern="[\w\d]*" type="text" name="lly_opt_player_preroll" placeholder="" value="<?php echo get_option('lly_opt_player_preroll'); ?>" /><br>
					        		<strong style="width:80px;display:inline-block"><?php esc_html_e( 'Post-roll', LL_TD ); ?></strong> <input pattern="^(?!,)[\w\d,]*[\w\d]$" type="text" name="lly_opt_player_postroll" placeholder="" value="<?php echo get_option('lly_opt_player_postroll'); ?>" /> <?php esc_html_e( '(multiple IDs allowed)', LL_TD ); ?><br>
					        		<br>
					        		<label>
										<?php printf( esc_html__( 'Convert all Youtube videos into a playlist and automatically add your corporate video, product teaser or another video advertisement. You have to insert the plain Youtube %1$s, like %2$s or a comma-separated list of video IDs (%3$s).', LL_TD ),
											'<b>video ID</b>',
											'<b>IJNR2EpS0jw</b>',
											'<i>IJNR2EpS0jw,dMH0bHeiRNg</i>'
										); ?>
					        		</label>
					        	</td>
					        </tr>
					        <tr valign="top">
					        	<th scope="row"><label><?php esc_html_e( 'Thumbnail quality', LL_TD ); ?></label></th>
						        <td>
									<select class="select" typle="select" name="lly_opt_thumbnail_quality">
										<option value="standard"<?php if (get_option('lly_opt_thumbnail_quality') === 'standard') { echo ' selected="selected"'; } ?>><?php esc_html_e( 'Standard quality', LL_TD ); ?></option>
										<option value="max"<?php if (get_option('lly_opt_thumbnail_quality') === 'max') { echo ' selected="selected"'; } ?>><?php esc_html_e( 'Max resolution', LL_TD ); ?></option>
									</select>
									<p><?php esc_html_e( 'Define which thumbnail quality should be used by default. When a maximum resolution thumbnail is not available, the standard thumbnail will be loaded. This setting can be overridden on every individual page/post.', LL_TD ); ?></p>
						        </td>
					        </tr>
					        <tr valign="top">
					        	<th scope="row"><label><?php esc_html_e( 'Colour of progress bar', LL_TD ); ?></label></th>
						        <td>
									<select class="select" typle="select" name="lly_opt_player_colour_progress">
										<option value="red"<?php if (get_option('lly_opt_player_colour_progress') === 'red') { echo ' selected="selected"'; } ?>><?php esc_html_e( 'Red (default)', LL_TD ); ?></option>
										<option value="white"<?php if (get_option('lly_opt_player_colour_progress') === 'white') { echo ' selected="selected"'; } ?>><?php esc_html_e( 'White', LL_TD ); ?></option>
									</select>
						        </td>
					        </tr>
					        <tr valign="top">
					        	<th scope="row"><label><?php esc_html_e( 'Hide annotations', LL_TD ); ?> <span class="newred grey">Tip</span></label></th>
						        <td>
									<input name="lly_opt_player_loadpolicy" type="checkbox" value="1" <?php checked( '1', get_option( 'lly_opt_player_loadpolicy' ) ); ?> /> <label><?php esc_html_e( 'If checked, video annotations (like "subscribe to channel") will not be shown.', LL_TD ); ?></label>
						        </td>
					        </tr>
					        <tr valign="top">
					        	<th scope="row"><label><?php esc_html_e( 'Hide player controls', LL_TD ); ?></label></th>
						        <td>
									<input name="lly_opt_player_controls" type="checkbox" value="1" <?php checked( '1', get_option( 'lly_opt_player_controls' ) ); ?> /> <label><?php esc_html_e( 'If checked, Youtube player controls will not be displayed.', LL_TD ); ?></label>
						        </td>
					        </tr>
					        <tr valign="top">
						        <th scope="row"><label><?php esc_html_e( 'Support for widgets', LL_TD ); ?></label></th>
						        <td>
									<input name="lly_opt_support_for_widgets" type="checkbox" value="1" <?php checked( '1', get_option( 'lly_opt_support_for_widgets' ) ); ?> /> <label><?php esc_html_e( 'Only check this box if you actually use this feature (for reason of performance)! If checked, you can paste a Youtube URL into a text widget and it will be lazy loaded.', LL_TD ); ?></label>
						        </td>
					        </tr>
			        	</tbody>
		        	</table>
		        </div>

				<div id="vimeo">

					<h3><?php esc_html_e( 'Lazy Load for Vimeo', LL_TD ); ?></h3>

					<table class="form-table">
						<tbody>
					        <tr valign="top">
						        <th scope="row"><label><?php esc_html_e( 'Disable Lazy Load for Vimeo', LL_TD ); ?></label></th>
						        <td>
									<input name="llv_opt" type="checkbox" value="1" <?php checked( '1', get_option( 'llv_opt' ) ); ?> /> 
									<label>
										<?php printf( esc_html__( 'If checked, Lazy Load will not be used for %1$s videos.', LL_TD ),
											'<b>Vimeo</b>'
										); ?>
									</label>
									<label><span style="color:#f60;"><?php esc_html_e( 'Important:', LL_TD ); ?></span> <?php esc_html_e( 'Updates on this option will only affect new posts and posts you update afterwards with the "Update Posts" button at the bottom of this form.', LL_TD ); ?></label>
						        </td>
					        </tr>
					        <tr valign="top">
						        <th scope="row"><label><?php esc_html_e( 'Display Vimeo title', LL_TD ); ?></label></th>
						        <td>
									<input name="llv_opt_title" type="checkbox" value="1" <?php checked( '1', get_option( 'llv_opt_title' ) ); ?> /> <label><?php esc_html_e( 'If checked, the Vimeo video title will be displayed on preview image.', LL_TD ); ?> <span style="color:#f60;"><?php esc_html_e( 'Important:', LL_TD ); ?></span> Titles won't be displayed if you've chosen to not load a thumbnail in the general tab of this settings page.</label>
						        </td>
					        </tr>
					        <tr valign="top">
					        	<th scope="row"><label><?php esc_html_e( 'Colour of the vimeo controls', LL_TD ); ?></label></th>
					        	<td>
					        		<input id="llv_picker_input_player_colour" class="ll_picker_player_colour picker-input" type="text" name="llv_opt_player_colour" data-default-color="#00adef" value="<?php if (get_option("llv_opt_player_colour") == "") { echo "#00adef"; } else { echo get_option("llv_opt_player_colour"); } ?>" />
					        	</td>
					        </tr>
			        	</tbody>
		        	</table>
		        </div>

				<?php do_action( 'lazyload_settings_page_tabs_after' ); ?>

			    <?php submit_button(); ?>
			</form>

	 		<div class="update-posts notice">
				<form action="options-general.php?page=<?php echo LL_ADMIN_URL; ?>" method="post">
				   <input type="hidden" name="update_posts" value="with_oembed" />
				   <input class="button update-posts" type="submit" value="Update Posts" />
				</form>
				<div class="help">
					<span class="tooltip-right info-icon" data-tooltip="Save changes first.">?</span> <span><?php esc_html_e( 'Update posts to setup your plugin for the first time or when recommended somewhere.', LL_TD ); ?></span>
				</div>
			</div>

			<?php require_once( 'inc/signup.php' ); ?>

		    <table class="form-table">
		        <tr valign="top">
		        <th scope="row" style="width:100px;"><a href="https://www.kweber.com/ll" target="_blank"><img src="https://www.gravatar.com/avatar/9d876cfd1fed468f71c84d26ca0e9e33?d=https%3A%2F%2F1.gravatar.com%2Favatar%2Fad516503a11cd5ca435acc9bb6523536&s=100" style="-webkit-border-radius:50%;-moz-border-radius:50%;border-radius:50%;"></a></th>
		        <td style="width:200px;">
		        	<p><a href="https://www.kweber.com/ll" target="_blank">Kevin Weber</a> &ndash; <?php esc_html_e( 'that\'s me.', LL_TD ); ?><br>
		        	<?php esc_html_e( 'I\'m the developer of this plugin. Love it!', LL_TD ); ?></p></td>
			        <td>
						<p>
							<b><?php esc_html_e( 'It\'s free!', LL_TD ); ?></b>
							<?php printf( esc_html__( 'Support me with %1$sa delicious lunch%2$s or give this plugin a 5 star rating %3$son WordPress.org%4$s.', LL_TD ),
								'<a href="https://www.kweber.com/donate/LazyLoadVideos/" title="Pay me a delicious lunch" target="_blank">',
								'</a>',
								'<a href="https://wordpress.org/support/view/plugin-reviews/lazy-load-for-videos?filter=5" title="Vote for Lazy Load for Videos" target="_blank">',
								'</a>'
							); ?>
						</p>
			        </td>
		        <td style="width:300px;">
					<p>
						<b><?php esc_html_e( 'Personal tip: Must use plugins', LL_TD ); ?></b>
						<ol>
							<li><a href="https://www.kweber.com/ll-wb" title="wBounce" target="_blank"><?php esc_html_e( 'wBounce', LL_TD ); ?></a> <?php esc_html_e( '(on my part)', LL_TD ); ?></li>
							<li><a href="https://yoast.com/wordpress/plugins/seo/" title="WordPress SEO by Yoast" target="_blank"><?php esc_html_e( 'WordPress SEO', LL_TD ); ?></a> <?php esc_html_e( '(by Yoast)', LL_TD ); ?></li>
							<li><a href="https://wordpress.org/plugins/broken-link-checker/" title="Broken Link Checker" target="_blank"><?php esc_html_e( 'Broken Link Checker', LL_TD ); ?></a> <?php esc_html_e( '(by Janis Elsts)', LL_TD ); ?></li>
						</ol>
					</p>
		        </td>
		        </tr>
			</table>
		</div>
	<?php
	}

	function lazyload_admin_js() {
		wp_enqueue_script( 'lazyload_admin_js', LL_URL . 'public/js/admin.js', array('jquery', 'jquery-ui-tabs', 'wp-color-picker' ), LL_VERSION );
	}

	function lazyload_admin_css() {
		wp_enqueue_style( 'lazyload-admin-css', LL_URL . 'public/css/admin.css' );
		wp_enqueue_style( 'wp-color-picker' );	// Required for colour picker
	}

}

function initialize_lazyloadvideos_admin() {
	new Lazy_Load_For_Videos_Admin();
}
add_action( 'init', 'initialize_lazyloadvideos_admin' );
