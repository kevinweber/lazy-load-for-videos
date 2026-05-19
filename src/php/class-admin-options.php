<?php
if (!defined('ABSPATH')) {
	exit;
}

/**
 * Create options panel (https://codex.wordpress.org/Creating_Options_Pages)
 */
class KW_LLV_Admin
{
	function __construct()
	{
		add_action('admin_init', array($this, 'admin_init'));
		add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
		// The 'oembed_dataparse' filter should be called on backend AND on frontend, not only on backend [is_admin()]. Otherwise, on some websites occur errors.
		add_filter('oembed_dataparse', array($this, 'lazyload_replace_video'), 10, 3);
		add_action('admin_menu', array($this, 'lazyload_create_menu'));
		$this->lazyloadvideos_update_posts_with_embed();
	}

	function admin_enqueue_scripts()
	{
		if (isset($_GET['page']) && ($_GET['page'] == LL_ADMIN_URL)) {
			$this->lazyload_admin_css();
			$this->lazyload_admin_js();
		}
	}

	function admin_init()
	{
		$plugin = plugin_basename(LL_FILE);
		add_filter("plugin_action_links_$plugin", array($this, 'lazyload_settings_link'));
		$this->register_lazyload_settings();
	}

	function isset_update_posts()
	{
		return isset($_POST['update_posts'])
			&& $_POST['update_posts'] == 'with_oembed'
			&& wp_verify_nonce($_POST['with_oembed_nonce'], 'lazyloadvideos_with_oembed_nonce');
	}

	/*
	 * Update posts with embed when user has clicked "Update Posts"
	 * @info: lazyloadvideos_update_posts_with_embed() is loaded by class-register.php
	 */
	function lazyloadvideos_update_posts_with_embed()
	{
		if ($this->isset_update_posts()) {
			KW_LLV_Update_Posts::delete_oembed_caches();
		}
	}

	/**
	 * Add settings link on plugin page
	 */
	function lazyload_settings_link($links)
	{
		$settings_link = '<a href="options-general.php?page=' . LL_ADMIN_URL . '">' . esc_html__('Settings', 'lazy-load-for-videos') . '</a>';
		array_unshift($links, $settings_link);
		return $links;
	}

	function text__no_script_fallback($title, $url)
	{
		$no_script_fallback = "<noscript>Video can't be loaded because JavaScript is disabled: <a href=\"" . esc_url($url) . "\" title=\"" . esc_attr($title) . "\">" . esc_attr($title) . " (" . esc_url($url) . ")</a></noscript>";

		return $no_script_fallback;
	}

	/**
	 * Replace embedded Youtube and Vimeo videos with a special piece of code.
	 * Thanks to Otto's comment on StackExchange (See https://wordpress.stackexchange.com/a/19533)
	 */
	function lazyload_replace_video($return, $data, $url)
	{
		global $lazyload_videos_general;

		// If URL contains "lazyload=0", we don't want to lazyload it.
		if (strpos($url, 'lazyload=0') !== false) {
			return $return;
		}

		// Replacements don't work on AMP pages (WordPress AMP plugin)
		if (function_exists('amp_is_request') && amp_is_request()) {
			return $return;
		}

		// Replacements don't work in feeds
		if (is_feed()) {
			return $return;
		}

		$data_title = isset($data->title) ? $data->title : '';
		$data_thumbnail = isset($data->thumbnail) ? $data->thumbnail : '';

		if (empty($data_thumbnail) && isset($data->thumbnail_url) && !empty($data->thumbnail_url)) {
			$data_thumbnail = $data->thumbnail_url;
		}

		// Youtube support
		if (
			($data->provider_name === 'YouTube')
			&& (get_option('lly_opt') == false) // test if Lazy Load for Youtube is deactivated
		) {

			$a_class = 'lazy-load-youtube preview-lazyload preview-youtube';
			$a_class = apply_filters('lazyload_preview_url_css_youtube', $a_class);

			$play_title_text = sprintf(
				/* translators: %s: video title */
				esc_attr__('Play video &quot;%s&quot;', 'lazy-load-for-videos'),
				$data_title
			);

			$preview_url = "<a href=\"" . esc_url($url) . "\" class=\"{$a_class}\" data-video-title=\"" . esc_attr($data_title) . "\" title=\"" . esc_attr($play_title_text) . "\">{$url}</a>";

			// Wrap container around $preview_url
			$preview_url = '<div class="container-lazyload preview-lazyload container-youtube js-lazyload--not-loaded">'
				. $preview_url
				. $this->text__no_script_fallback($data_title, $url)
				. '</div>';

			return apply_filters('lazyload_replace_video_preview_url_youtube', $preview_url);
		}

		// Vimeo support
		elseif (
			$data->provider_name === 'Vimeo'
			&& (get_option('llv_opt') == false) // test if Lazy Load for Vimeo is deactivated
		) {
			$a_class = 'lazy-load-vimeo preview-lazyload preview-vimeo';
			$a_class = apply_filters('lazyload_preview_url_css_vimeo', $a_class);

			$play_title_text = sprintf(
				/* translators: %s: video title */
				esc_attr__('Play video &quot;%s&quot;', 'lazy-load-for-videos'),
				$data_title
			);

			$preview_url = "<a href=\"" . esc_url($url) . "\" id=\"{$data->video_id}\" class=\"{$a_class}\" data-video-uri=\"$data->uri\" data-video-thumbnail=\"{$data_thumbnail}\" data-video-title=\"" . esc_attr($data_title) . "\" title=\"" . esc_attr($play_title_text) . "\">{$url}</a>";

			// Wrap container around $preview_url
			$preview_url = '<div class="container-lazyload container-vimeo js-lazyload--not-loaded">'
				. $preview_url
				. $this->text__no_script_fallback($data_title, $url)
				. '</div>';

			return apply_filters('lazyload_replace_video_preview_url_vimeo', $preview_url);
		} else
			return $return;
	}

	function lazyload_create_menu()
	{
		add_options_page(esc_html__('Lazy Load for Videos', 'lazy-load-for-videos'), esc_html__('Lazy Load for Videos', 'lazy-load-for-videos'), 'manage_options', LL_ADMIN_URL, array($this, 'lazyload_settings_page'));
	}

	function register_lazyload_settings()
	{
		$settings = array(
			// General/Styling
			'll_opt_load_scripts' => array(
				'type' => 'string',
				'sanitize_callback' => array($this, 'sanitize_checkbox'),
				'default' => '',
			),
			'll_opt_button_style' => array(
				'type' => 'string',
				'sanitize_callback' => array($this, 'sanitize_button_style'),
				'default' => 'default',
			),
			'll_opt_thumbnail_size' => array(
				'type' => 'string',
				'sanitize_callback' => array($this, 'sanitize_thumbnail_size'),
				'default' => 'cover',
			),
			'll_opt_thumbnail_quality' => array(
				'type' => 'string',
				'sanitize_callback' => array($this, 'sanitize_thumbnail_quality'),
				'default' => 'basic',
			),
			'll_opt_customcss' => array(
				'type' => 'string',
				'sanitize_callback' => array($this, 'sanitize_customcss'),
				'default' => '',
			),
			'll_opt_support_for_tablepress' => array(
				'type' => 'string',
				'sanitize_callback' => array($this, 'sanitize_checkbox'),
				'default' => '',
			),
			'll_attribute' => array(
				'type' => 'string',
				'sanitize_callback' => array($this, 'sanitize_attribute'),
				'default' => 'none',
			),

			// Youtube
			'lly_opt' => array(
				'type' => 'string',
				'sanitize_callback' => array($this, 'sanitize_checkbox'),
				'default' => '',
			),
			'lly_opt_title' => array(
				'type' => 'string',
				'sanitize_callback' => array($this, 'sanitize_checkbox'),
				'default' => '',
			),
			'lly_opt_overlay_text' => array(
				'type' => 'string',
				'sanitize_callback' => array($this, 'sanitize_youtube_overlay_text'),
				'default' => '',
			),
			'lly_opt_player_preroll' => array(
				'type' => 'string',
				'sanitize_callback' => array($this, 'sanitize_preroll'),
				'default' => '',
			),
			'lly_opt_player_postroll' => array(
				'type' => 'string',
				'sanitize_callback' => array($this, 'sanitize_postroll'),
				'default' => '',
			),
			'lly_opt_support_for_widgets' => array(
				'type' => 'string',
				'sanitize_callback' => array($this, 'sanitize_checkbox'),
				'default' => '',
			),
			'lly_opt_player_colour_progress' => array(
				'type' => 'string',
				'sanitize_callback' => array($this, 'sanitize_youtube_player_colour'),
				'default' => 'red',
			),
			'lly_opt_player_controls' => array(
				'type' => 'string',
				'sanitize_callback' => array($this, 'sanitize_checkbox'),
				'default' => '',
			),
			'lly_opt_player_loadpolicy' => array(
				'type' => 'string',
				'sanitize_callback' => array($this, 'sanitize_checkbox'),
				'default' => '',
			),
			'lly_opt_cookies' => array(
				'type' => 'string',
				'sanitize_callback' => array($this, 'sanitize_checkbox'),
				'default' => '',
			),

			// Vimeo
			'llv_opt' => array(
				'type' => 'string',
				'sanitize_callback' => array($this, 'sanitize_checkbox'),
				'default' => '',
			),
			'llv_opt_title' => array(
				'type' => 'string',
				'sanitize_callback' => array($this, 'sanitize_checkbox'),
				'default' => '',
			),
			'llv_opt_overlay_text' => array(
				'type' => 'string',
				'sanitize_callback' => array($this, 'sanitize_vimeo_overlay_text'),
				'default' => '',
			),
			'llv_opt_player_colour' => array(
				'type' => 'string',
				'sanitize_callback' => array($this, 'sanitize_hex_color_custom'),
				'default' => '#00adef',
			),
			'llv_opt_cookies' => array(
				'type' => 'string',
				'sanitize_callback' => array($this, 'sanitize_checkbox'),
				'default' => '',
			),
		);

		foreach ($settings as $option_name => $args) {
			register_setting('ll-settings-group', $option_name, $args);
		}
		do_action('lazyload_register_settings_after');
	}

	function lazyload_settings_page()
	{ ?>

		<?php if ($this->isset_update_posts()) { ?>
			<div class="update-posts updated">
				<p><?php esc_html_e('Your posts have been updated successfully.', 'lazy-load-for-videos'); ?></p>
			</div>
		<?php } ?>

		<div id="tabs" class="ui-tabs">
			<h1><?php esc_html_e('Lazy Load for Videos', 'lazy-load-for-videos'); ?> <span
					class="subtitle"><?php esc_html_e('by', 'lazy-load-for-videos'); ?> <a href="https://www.kweber.com/"
						target="_blank" title="<?php esc_html_e('Website by Kevin Weber', 'lazy-load-for-videos'); ?>">Kevin
						Weber</a> (<?php esc_html_e('Version', 'lazy-load-for-videos'); ?>
					<?php echo esc_html(LL_VERSION); ?>)</span></h1>
			<h2 class="claim" style="font-size:15px;font-style:italic;position:relative;top:-10px;">
				<?php esc_html_e('Speed up your site and customise your video player!', 'lazy-load-for-videos'); ?></h2>

			<ul class="nav-tab-wrapper">
				<li class="nav-tab"><a href="#general"><?php esc_html_e('General/Styling', 'lazy-load-for-videos'); ?></a>
				</li>
				<li class="nav-tab"><a href="#youtube"><?php esc_html_e('YouTube', 'lazy-load-for-videos'); ?></a></li>
				<li class="nav-tab"><a href="#vimeo"><?php esc_html_e('Vimeo', 'lazy-load-for-videos'); ?><span
							class="newred_dot">&bull;</span></a></li>
				<?php do_action('lazyload_settings_page_tabs_link_after'); ?>
			</ul>

			<form method="post" action="options.php">
				<?php
				settings_fields('ll-settings-group');
				do_settings_sections('ll-settings-group');
				?>


				<div id="general">

					<h3><?php esc_html_e('General/Styling', 'lazy-load-for-videos'); ?></h3>

					<table class="form-table">
						<tbody>
							<tr valign="top">
								<th scope="row">
									<label><?php esc_html_e('Only load CSS/JS when needed', 'lazy-load-for-videos'); ?><br><span
											class="description thin"><?php esc_html_e('to improve performance', 'lazy-load-for-videos'); ?></span></label>
								</th>
								<td>
									<input name="ll_opt_load_scripts" type="checkbox" value="1" <?php checked('1', get_option('ll_opt_load_scripts')); ?> /> <label><span
											style="color:#f60;"><?php esc_html_e('Important:', 'lazy-load-for-videos'); ?></span>
										<?php esc_html_e('When this option is checked, some videos might not lazy load if posts with videos are loaded using Ajax.', 'lazy-load-for-videos'); ?></label>
								</td>
							</tr>
							<tr valign="top">
								<th scope="row"><label><?php esc_html_e('Play Button', 'lazy-load-for-videos'); ?></label>
								</th>
								<td>
									<select class="select" typle="select" name="ll_opt_button_style">
										<option value="default" <?php if (get_option('ll_opt_button_style') === 'default') {
											echo ' selected="selected"';
										} ?>>
											<?php esc_html_e('White (CSS-only)', 'lazy-load-for-videos'); ?></option>
										<option value="css_white_pulse" <?php if (get_option('ll_opt_button_style') === 'css_white_pulse') {
											echo ' selected="selected"';
										} ?>>
											<?php esc_html_e('White Pulse (CSS-only)', 'lazy-load-for-videos'); ?></option>
										<option value="css_black" <?php if (get_option('ll_opt_button_style') === 'css_black') {
											echo ' selected="selected"';
										} ?>>
											<?php esc_html_e('Black (CSS-only)', 'lazy-load-for-videos'); ?></option>
										<option value="css_black_pulse" <?php if (get_option('ll_opt_button_style') === 'css_black_pulse') {
											echo ' selected="selected"';
										} ?>>
											<?php esc_html_e('Black Pulse (CSS-only)', 'lazy-load-for-videos'); ?></option>
										<option value="youtube_button_image" <?php if (get_option('ll_opt_button_style') === 'youtube_button_image') {
											echo ' selected="selected"';
										} ?>>
											<?php esc_html_e('Old Youtube button image', 'lazy-load-for-videos'); ?></option>
										<option value="youtube_button_image_red" <?php if (get_option('ll_opt_button_style') === 'youtube_button_image_red') {
											echo ' selected="selected"';
										} ?>>
											<?php esc_html_e('Red Youtube button image', 'lazy-load-for-videos'); ?></option>
										<option value="none" <?php if (get_option('ll_opt_button_style') === 'none') {
											echo ' selected="selected"';
										} ?>>
											<?php esc_html_e('No play button', 'lazy-load-for-videos'); ?></option>
									</select>
								</td>
							</tr>
							<tr valign="top">
								<th scope="row">
									<label><?php esc_html_e('Thumbnails/Patterns', 'lazy-load-for-videos'); ?></label></th>
								<td>
									<select class="select" typle="select" name="ll_opt_thumbnail_size">
										<option value="cover" <?php if (get_option('ll_opt_thumbnail_size') === 'cover') {
											echo ' selected="selected"';
										} ?>>
											<?php esc_html_e('Thumbnail covering the video element', 'lazy-load-for-videos'); ?>
										</option>
										<option value="standard" <?php if (get_option('ll_opt_thumbnail_size') === 'standard') {
											echo ' selected="selected"';
										} ?>>
											<?php esc_html_e('Thumbnail contained within the video element', 'lazy-load-for-videos'); ?>
										</option>
										<option value="pattern-carbon" <?php if (get_option('ll_opt_thumbnail_size') === 'pattern-carbon') {
											echo ' selected="selected"';
										} ?>>
											<?php esc_html_e('Pattern: Carbon', 'lazy-load-for-videos'); ?></option>
										<option value="pattern-dots" <?php if (get_option('ll_opt_thumbnail_size') === 'pattern-dots') {
											echo ' selected="selected"';
										} ?>>
											<?php esc_html_e('Pattern: Dots', 'lazy-load-for-videos'); ?></option>
										<option value="pattern-light-s" <?php if (get_option('ll_opt_thumbnail_size') === 'pattern-light-s') {
											echo ' selected="selected"';
										} ?>>
											<?php esc_html_e('Pattern: Light Seigaiha', 'lazy-load-for-videos'); ?></option>
										<option value="none" <?php if (get_option('ll_opt_thumbnail_size') === 'none') {
											echo ' selected="selected"';
										} ?>><?php esc_html_e('None', 'lazy-load-for-videos'); ?>
										</option>
									</select>
									<p><?php esc_html_e('For a thumbnail to be displayed, a request needs to be made to the server of a video platform. You can display one of the available patterns instead, or nothing at all.', 'lazy-load-for-videos'); ?>
									</p>
								</td>
							</tr>
							<tr valign="top">
								<th scope="row">
									<label><?php esc_html_e('Thumbnail quality', 'lazy-load-for-videos'); ?></label></th>
								<td>
									<select class="select" typle="select" name="ll_opt_thumbnail_quality">
										<option value="basic" <?php if (get_option('ll_opt_thumbnail_quality') === 'basic') {
											echo ' selected="selected"';
										} ?>>
											<?php esc_html_e('Standard quality', 'lazy-load-for-videos'); ?></option>
										<option value="medium" <?php if (get_option('ll_opt_thumbnail_quality') === 'medium') {
											echo ' selected="selected"';
										} ?>>
											<?php esc_html_e('Higher quality', 'lazy-load-for-videos'); ?></option>
										<option value="max" <?php if (get_option('ll_opt_thumbnail_quality') === 'max') {
											echo ' selected="selected"';
										} ?>>
											<?php esc_html_e('Max resolution', 'lazy-load-for-videos'); ?></option>
									</select>
									<p><?php esc_html_e('Define which thumbnail quality should be used by default. When a maximum resolution thumbnail is not available, a lower quality thumbnail will be loaded. This setting can be overridden on every individual page/post.', 'lazy-load-for-videos'); ?>
									</p>
								</td>
							</tr>
							<tr valign="top">
								<th scope="row"><label><?php esc_html_e('Custom CSS', 'lazy-load-for-videos'); ?></label></th>
								<td>
									<textarea rows="14" cols="70" type="text"
										name="ll_opt_customcss"><?php echo esc_textarea(get_option('ll_opt_customcss')); ?></textarea>
								</td>
							</tr>
							<tr valign="top">
								<th scope="row">
									<label><?php esc_html_e('Support for TablePress', 'lazy-load-for-videos'); ?></label></th>
								<td>
									<input name="ll_opt_support_for_tablepress" type="checkbox" value="1" <?php checked('1', get_option('ll_opt_support_for_tablepress')); ?> />
									<label><?php esc_html_e('Only check this box if you actually use this feature (for reason of performance). If checked, you can paste a Youtube or Vimeo URL into tables that are created with TablePress and it will be lazy loaded.', 'lazy-load-for-videos'); ?></label>
								</td>
							</tr>
							<tr valign="top">
								<th scope="row"><?php esc_html_e('Attribution', 'lazy-load-for-videos'); ?><br><span
										class="description thin"><?php esc_html_e("Are you thankful for this plugin? I've invested months and months of work. This plugin is 100% free and open source.", 'lazy-load-for-videos'); ?></span>
								</th>
								<td>
									<?php $options = get_option('ll_attribute'); ?>
									<input class="radio" type="radio" name="ll_attribute" value="none" <?php checked('none' == $options || empty($options)); ?> /> <label
										for="none"><?php esc_html_e('No attribution: "I can not afford to give appropriate credit for this free plugin."', 'lazy-load-for-videos'); ?></label><br><br>
									<input class="radio" type="radio" name="ll_attribute" value="donate" <?php checked('donate' == $options); ?> />
									<label for="donate">
										<?php esc_html_e('Donation: "I have donated already or will do so soon."', 'lazy-load-for-videos'); ?>
										<?php
										printf(
											/* translators: %1$s: opening anchor tag for donation link, %2$s: closing anchor tag */
											esc_html__('Please %1$sdonate now%2$s so I can keep maintaining and improving this plugin.', 'lazy-load-for-videos'),
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

					<h3><?php esc_html_e('Lazy Load for Youtube', 'lazy-load-for-videos'); ?></h3>

					<table class="form-table">
						<tbody>
							<tr valign="top">
								<th scope="row">
									<label><?php esc_html_e('Disable Lazy Load for Youtube', 'lazy-load-for-videos'); ?></label>
								</th>
								<td>
									<input name="lly_opt" type="checkbox" value="1" <?php checked('1', get_option('lly_opt')); ?> />
									<label>
										<?php
										printf(
											/* translators: %1$s: Video provider name (e.g. YouTube) */
											esc_html__('If checked, Lazy Load will not be used for %1$s videos.', 'lazy-load-for-videos'),
											'<b>Youtube</b>'
										); ?>
									</label>
									<label><span
											style="color:#f60;"><?php esc_html_e('Important:', 'lazy-load-for-videos'); ?></span>
										<?php esc_html_e('Updates on this option will only affect new posts and posts you update afterwards with the "Update Posts" button at the bottom of this form.', 'lazy-load-for-videos'); ?></label>
								</td>
							</tr>
							<tr valign="top">
								<th scope="row">
									<label><?php esc_html_e('Display Youtube title', 'lazy-load-for-videos'); ?></label></th>
								<td>
									<input name="lly_opt_title" type="checkbox" value="1" <?php checked('1', get_option('lly_opt_title')); ?> />
									<label><?php esc_html_e('If checked, the Youtube video title will be displayed on preview image.', 'lazy-load-for-videos'); ?></label>
								</td>
							</tr>
							<tr valign="top">
								<th scope="row"><label><?php esc_html_e('Text Overlay', 'lazy-load-for-videos'); ?></label>
								</th>
								<td>
									<textarea rows="4" cols="70" type="text"
										name="lly_opt_overlay_text"><?php echo esc_textarea(get_option('lly_opt_overlay_text')); ?></textarea>
									<br>
									<p><?php esc_html_e('Enter text to be displayed on top of all video thumbnails, for example a privacy disclaimer. Supports HTML.', 'lazy-load-for-videos'); ?>
									</p>
								</td>
							</tr>
							<tr valign="top">
								<th scope="row">
									<label><?php esc_html_e('Pre-roll/post-roll ads', 'lazy-load-for-videos'); ?><span
											class="description thin"><br>Sell advertising space!</span></label></th>
								<td>
									<strong
										style="width:80px;display:inline-block"><?php esc_html_e('Pre-roll', 'lazy-load-for-videos'); ?></strong>
									<input pattern="[\w\d]*" type="text" name="lly_opt_player_preroll" placeholder=""
										value="<?php echo esc_attr(get_option('lly_opt_player_preroll')); ?>" /><br>
									<strong
										style="width:80px;display:inline-block"><?php esc_html_e('Post-roll', 'lazy-load-for-videos'); ?></strong>
									<input pattern="^(?!,)[\w\d,]*[\w\d]$" type="text" name="lly_opt_player_postroll"
										placeholder="" value="<?php echo esc_attr(get_option('lly_opt_player_postroll')); ?>" />
									<?php esc_html_e('(multiple IDs allowed)', 'lazy-load-for-videos'); ?><br>
									<br>
									<label>
										<?php
										printf(
											/* translators: %1$s: video ID bold text, %2$s: example video ID bold text, %3$s: example list of video IDs italic text */
											esc_html__('Convert all Youtube videos into a playlist and automatically add your corporate video, product teaser or another video advertisement. You have to insert the plain Youtube %1$s, like %2$s or a comma-separated list of video IDs (%3$s).', 'lazy-load-for-videos'),
											'<b>video ID</b>',
											'<b>IJNR2EpS0jw</b>',
											'<i>IJNR2EpS0jw,dMH0bHeiRNg</i>'
										); ?>
									</label>
								</td>
							</tr>
							<tr valign="top">
								<th scope="row">
									<label><?php esc_html_e('Colour of progress bar', 'lazy-load-for-videos'); ?></label></th>
								<td>
									<select class="select" typle="select" name="lly_opt_player_colour_progress">
										<option value="red" <?php if (get_option('lly_opt_player_colour_progress') === 'red') {
											echo ' selected="selected"';
										} ?>>
											<?php esc_html_e('Red (default)', 'lazy-load-for-videos'); ?></option>
										<option value="white" <?php if (get_option('lly_opt_player_colour_progress') === 'white') {
											echo ' selected="selected"';
										} ?>>
											<?php esc_html_e('White', 'lazy-load-for-videos'); ?></option>
									</select>
								</td>
							</tr>
							<tr valign="top">
								<th scope="row">
									<label><?php esc_html_e('Hide annotations', 'lazy-load-for-videos'); ?></label></th>
								<td>
									<input name="lly_opt_player_loadpolicy" type="checkbox" value="1" <?php checked('1', get_option('lly_opt_player_loadpolicy')); ?> />
									<label><?php esc_html_e('If checked, video annotations (like "subscribe to channel") will not be shown.', 'lazy-load-for-videos'); ?></label>
								</td>
							</tr>
							<tr valign="top">
								<th scope="row">
									<label><?php esc_html_e('Hide player controls', 'lazy-load-for-videos'); ?></label></th>
								<td>
									<input name="lly_opt_player_controls" type="checkbox" value="1" <?php checked('1', get_option('lly_opt_player_controls')); ?> />
									<label><?php esc_html_e('If checked, Youtube player controls will not be displayed.', 'lazy-load-for-videos'); ?></label>
								</td>
							</tr>
							<tr valign="top">
								<th scope="row">
									<label><?php esc_html_e('Enable Youtube cookies', 'lazy-load-for-videos'); ?></label></th>
								<td>
									<input name="lly_opt_cookies" type="checkbox" value="1" <?php checked('1', get_option('lly_opt_cookies')); ?> />
									<label><?php esc_html_e('If checked, Youtube videos will have a URL with "youtube.com" instead of "youtube-nocookie.com". This means Youtube video creators will get more extensive tracking stats at the expense of the privacy of your users.', 'lazy-load-for-videos'); ?></label>
								</td>
							</tr>
							<tr valign="top">
								<th scope="row">
									<label><?php esc_html_e('Support for widgets', 'lazy-load-for-videos'); ?></label></th>
								<td>
									<input name="lly_opt_support_for_widgets" type="checkbox" value="1" <?php checked('1', get_option('lly_opt_support_for_widgets')); ?> />
									<label><?php esc_html_e('Only check this box if you actually use this feature (for reason of performance)! If checked, you can paste a Youtube URL into a text widget and it will be lazy loaded.', 'lazy-load-for-videos'); ?></label>
								</td>
							</tr>
						</tbody>
					</table>
				</div>

				<div id="vimeo">

					<h3><?php esc_html_e('Lazy Load for Vimeo', 'lazy-load-for-videos'); ?></h3>

					<table class="form-table">
						<tbody>
							<tr valign="top">
								<th scope="row">
									<label><?php esc_html_e('Disable Lazy Load for Vimeo', 'lazy-load-for-videos'); ?></label>
								</th>
								<td>
									<input name="llv_opt" type="checkbox" value="1" <?php checked('1', get_option('llv_opt')); ?> />
									<label>
										<?php
										printf(
											/* translators: %1$s: Video provider name (e.g. Vimeo) */
											esc_html__('If checked, Lazy Load will not be used for %1$s videos.', 'lazy-load-for-videos'),
											'<b>Vimeo</b>'
										); ?>
									</label>
									<label><span
											style="color:#f60;"><?php esc_html_e('Important:', 'lazy-load-for-videos'); ?></span>
										<?php esc_html_e('Updates on this option will only affect new posts and posts you update afterwards with the "Update Posts" button at the bottom of this form.', 'lazy-load-for-videos'); ?></label>
								</td>
							</tr>
							<tr valign="top">
								<th scope="row">
									<label><?php esc_html_e('Display Vimeo title', 'lazy-load-for-videos'); ?></label></th>
								<td>
									<input name="llv_opt_title" type="checkbox" value="1" <?php checked('1', get_option('llv_opt_title')); ?> />
									<label><?php esc_html_e('If checked, the Vimeo video title will be displayed on preview image.', 'lazy-load-for-videos'); ?>
										<span
											style="color:#f60;"><?php esc_html_e('Important:', 'lazy-load-for-videos'); ?></span>
										Titles won't be displayed if you've chosen to not load a thumbnail in the general tab of
										this settings page.</label>
								</td>
							</tr>
							<tr valign="top">
								<th scope="row"><label><?php esc_html_e('Text Overlay', 'lazy-load-for-videos'); ?></label>
								</th>
								<td>
									<textarea rows="4" cols="70" type="text"
										name="llv_opt_overlay_text"><?php echo esc_textarea(get_option('llv_opt_overlay_text')); ?></textarea>
									<br>
									<p><?php esc_html_e('Enter text to be displayed on top of all video thumbnails, for example a privacy disclaimer. No HTML.', 'lazy-load-for-videos'); ?>
									</p>
								</td>
							</tr>
							<tr valign="top">
								<th scope="row">
									<label><?php esc_html_e('Colour of the vimeo controls', 'lazy-load-for-videos'); ?></label>
								</th>
								<td>
									<input id="llv_picker_input_player_colour" class="ll_picker_player_colour picker-input"
										type="text" name="llv_opt_player_colour" data-default-color="#00adef"
										value="<?php if (get_option("llv_opt_player_colour") == "") {
											echo "#00adef";
										} else {
											echo esc_attr(get_option("llv_opt_player_colour"));
										} ?>" />
								</td>
							</tr>
							<tr valign="top">
								<th scope="row">
									<label><?php esc_html_e('Enable Vimeo cookies', 'lazy-load-for-videos'); ?><span
											class="newred">v2.17.0: New feature</span></label></th>
								<td>
									<input name="llv_opt_cookies" type="checkbox" value="1" <?php checked('1', get_option('llv_opt_cookies')); ?> />
									<label><?php esc_html_e('If checked, Vimeo videos will have a URL with "dnt=0" instead of "dnt=1". This means that Vimeo video creators will get more extensive tracking stats at the expense of the privacy of your users.', 'lazy-load-for-videos'); ?></label>
								</td>
							</tr>
						</tbody>
					</table>
				</div>

				<?php do_action('lazyload_settings_page_tabs_after'); ?>

				<?php submit_button(); ?>
			</form>

			<div class="update-posts notice">
				<form action="options-general.php?page=<?php echo esc_attr(LL_ADMIN_URL); ?>" method="post">
					<?php wp_nonce_field('lazyloadvideos_with_oembed_nonce', 'with_oembed_nonce'); ?>
					<input type="hidden" name="update_posts" value="with_oembed" />
					<button class="button update-posts" type="submit"
						value="Update Posts"><?php esc_html_e('Update Posts', 'lazy-load-for-videos'); ?></button>
				</form>
				<div class="help">
					<span class="tooltip-right info-icon"
						data-tooltip="<?php esc_html_e('Save changes first.', 'lazy-load-for-videos'); ?>">?</span>
					<span><?php esc_html_e('Update posts to setup your plugin for the first time or when recommended somewhere.', 'lazy-load-for-videos'); ?></span>
				</div>
			</div>

			<?php require_once('inc/signup.php'); ?>

			<table class="form-table">
				<tr valign="top">
					<th scope="row" style="width:100px;"><a href="https://www.kweber.com/" target="_blank"><img
								src="https://www.gravatar.com/avatar/9d876cfd1fed468f71c84d26ca0e9e33?d=https%3A%2F%2F1.gravatar.com%2Favatar%2Fad516503a11cd5ca435acc9bb6523536&s=100"
								style="-webkit-border-radius:50%;-moz-border-radius:50%;border-radius:50%;"></a></th>
					<td style="width:200px;">
						<p><a href="https://www.kweber.com/" target="_blank">Kevin Weber</a> &ndash;
							<?php esc_html_e('that\'s me.', 'lazy-load-for-videos'); ?><br>
							<?php esc_html_e('I\'m the developer of this plugin. Love it!', 'lazy-load-for-videos'); ?></p>
					</td>
					<td>
						<p>
							<b><?php esc_html_e('It\'s free!', 'lazy-load-for-videos'); ?></b>
							<?php
							printf(
								/* translators: %1$s: opening anchor tag for donation link, %2$s: closing anchor tag, %3$s: opening anchor tag for review link, %4$s: closing anchor tag */
								esc_html__('Support me with %1$sa delicious lunch%2$s or give this plugin a 5 star rating %3$son WordPress.org%4$s.', 'lazy-load-for-videos'),
								'<a href="https://www.kweber.com/donate/LazyLoadVideos/" title="Pay me a delicious lunch" target="_blank">',
								'</a>',
								'<a href="https://wordpress.org/support/view/plugin-reviews/lazy-load-for-videos?filter=5" title="Vote for Lazy Load for Videos" target="_blank">',
								'</a>'
							); ?>
						</p>
					</td>
				</tr>
			</table>
		</div>
		<?php
	}

	function lazyload_admin_js()
	{
		wp_enqueue_script('lazyload_admin_js', LL_URL . 'public/js/admin.js', array('jquery', 'jquery-ui-tabs', 'wp-color-picker'), LL_VERSION);
	}

	function lazyload_admin_css()
	{
		wp_enqueue_style('lazyload-admin-css', LL_URL . 'public/css/admin.css');
		wp_enqueue_style('wp-color-picker');	// Required for colour picker
	}

	/**
	 * Sanitizes a checkbox option.
	 */
	public function sanitize_checkbox($value)
	{
		return ('1' === $value) ? '1' : '';
	}

	/**
	 * Sanitizes the play button style option.
	 */
	public function sanitize_button_style($value)
	{
		$valid = array('default', 'css_white_pulse', 'css_black', 'css_black_pulse', 'youtube_button_image', 'youtube_button_image_red', 'none');
		return in_array($value, $valid, true) ? $value : 'default';
	}

	/**
	 * Sanitizes the thumbnail size option.
	 */
	public function sanitize_thumbnail_size($value)
	{
		$valid = array('cover', 'standard', 'pattern-carbon', 'pattern-dots', 'pattern-light-s', 'none');
		return in_array($value, $valid, true) ? $value : 'cover';
	}

	/**
	 * Sanitizes the thumbnail quality option.
	 */
	public function sanitize_thumbnail_quality($value)
	{
		$valid = array('basic', 'medium', 'max');
		return in_array($value, $valid, true) ? $value : 'basic';
	}

	/**
	 * Sanitizes the custom CSS option.
	 */
	public function sanitize_customcss($value)
	{
		return wp_strip_all_tags($value);
	}

	/**
	 * Sanitizes the attribution option.
	 */
	public function sanitize_attribute($value)
	{
		$valid = array('none', 'donate');
		return in_array($value, $valid, true) ? $value : 'none';
	}

	/**
	 * Sanitizes the YouTube overlay text option (allowing safe HTML).
	 */
	public function sanitize_youtube_overlay_text($value)
	{
		return wp_kses_post($value);
	}

	/**
	 * Sanitizes the Vimeo overlay text option (no HTML allowed).
	 */
	public function sanitize_vimeo_overlay_text($value)
	{
		return sanitize_textarea_field($value);
	}

	/**
	 * Sanitizes YouTube preroll video ID.
	 */
	public function sanitize_preroll($value)
	{
		return preg_replace('/[^A-Za-z0-9_-]/', '', $value);
	}

	/**
	 * Sanitizes YouTube postroll video IDs (comma-separated).
	 */
	public function sanitize_postroll($value)
	{
		return preg_replace('/[^A-Za-z0-9_,-]/', '', $value);
	}

	/**
	 * Sanitizes YouTube player color option.
	 */
	public function sanitize_youtube_player_colour($value)
	{
		return in_array($value, array('red', 'white'), true) ? $value : 'red';
	}

	/**
	 * Sanitizes Vimeo player color option (Hex color or empty).
	 */
	public function sanitize_hex_color_custom($value)
	{
		if (empty($value)) {
			return '';
		}
		$sanitized = sanitize_hex_color($value);
		return $sanitized ? $sanitized : '';
	}

}

new KW_LLV_Admin();
