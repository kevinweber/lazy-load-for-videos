<?php
/*
 * Plugin Name: Lazy Load for Videos
 * Plugin URI: http://kevinw.de/lazyloadvideos.php
 * Description: The Lazy Load for Videos plugin speeds up your site by replacing embedded Youtube and Vimeo videos with a clickable preview image. Visitors simply click on the image to play the video.
 * Author: Kevin Weber
 * Version: 1.3.0.1
 * Author URI: http://kevinw.de/
 * License: GPL2+
 * Text Domain: lazy-load-videos
*/

/***** Part 1: Replace embedded Youtube and Vimeo videos with a special piece of code (required for Part 3) */
/* Thanks to Otto's comment on StackExchange (See http://wordpress.stackexchange.com/a/19533) */
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
	add_filter('oembed_dataparse','lazyload_replace_video', 10, 3);


/***** Part 2: Enable jQuery (comes with WordPress) */
	function lazyload_enqueue_jquery() {
    	wp_enqueue_script('jquery');
	}
	add_action( 'wp_enqueue_scripts', 'lazyload_enqueue_jquery' );


/***** Part 3a: Lazy Load YOUTUBE Videos (Load youtube script and video after clicking on the preview image) */
/* Thanks to »Lazy loading of youtube videos by MS-potilas 2012« (see http://yabtb.blogspot.com/2012/02/youtube-videos-lazy-load-improved-style.html) */
	function enable_lazyload_youtube() { ?>
	    <script type='text/javascript'>
	    var $lly = jQuery.noConflict();
	    $lly(document).ready(function() {

			function doload_lly() {

		      $lly("a.lazy-load-youtube").each(function(index) {
		        var embedparms = $lly(this).attr("href").split("/embed/")[1];
		        if(!embedparms) embedparms = $lly(this).attr("href").split("://youtu.be/")[1];
		        if(!embedparms) embedparms = $lly(this).attr("href").split("v=")[1].replace(/\&/,'?');
		        var youid = embedparms.split("?")[0].split("#")[0];
		        var start = embedparms.match(/[#&]t=(\d+)s/);
		        if(start) start = start[1];
		        else {
		          start = embedparms.match(/[#&]t=(\d+)m(\d+)s/);
		          if(start) start = parseInt(start[1])*60+parseInt(start[2]);
		          else {
		            start = embedparms.match(/[?&]start=(\d+)/);
		            if(start) start = start[1];
		          }
		        }
		        embedparms = embedparms.split("#")[0];
		        if(start && embedparms.indexOf("start=") == -1)
		          embedparms += ((embedparms.indexOf("?")==-1) ? "?" : "&") + "start="+start;
		        if(embedparms.indexOf("showinfo=0") != -1)
		          $lly(this).html('');
		        else
		          $lly(this).html('<div class="lazy-load-youtube-info"><span class="titletext youtube">' + $lly(this).html() + '</div></div>');
		        $lly(this).prepend('<div style="height:'+(parseInt($lly(this).css("height"))-4)+'px;width:'+(parseInt($lly(this).css("width"))-4)+'px;" class="lazy-load-youtube-div"></div>');
		        $lly(this).css("background", "#000 url(http://i2.ytimg.com/vi/"+youid+"/0.jpg) center center no-repeat");
		        $lly(this).attr("id", youid+index);
		        $lly(this).attr("href", "http://www.youtube.com/watch?v="+youid+(start ? "#t="+start+"s" : ""));
		        var emu = 'http://www.youtube.com/embed/'+embedparms;
		        emu += ((emu.indexOf("?")==-1) ? "?" : "&") + "autoplay=1";
		        var videoFrame = '<iframe width="'+parseInt($lly(this).css("width"))+'" height="'+parseInt($lly(this).css("height"))+'" style="vertical-align:top;" src="'+emu+'" frameborder="0" allowfullscreen></iframe>';
		        $lly(this).attr("onclick", "$lly('#"+youid+index+"').replaceWith('"+videoFrame+"');return false;");
		      });

			}

			$lly(document).ready(doload_lly()).ajaxStop(function(){
				doload_lly();
			});

	    })
	    </script>
	<?php }    
	add_action('wp_head', 'enable_lazyload_youtube');


/***** Part 3b: Lazy Load VIMEO Videos (Load vimeo script and video after clicking on the preview image) */
/* Lazy Load for Vimeo works with URLs that look like: [Any Path]/[Video ID]
Examples:
http://vimeo.com/channels/staffpicks/48851874
http://vimeo.com/48851874
http://vimeo.com/48851874/
*/
	function enable_lazyload_vimeo() { ?>
	    <script type='text/javascript'>

        var $llv = jQuery.noConflict();

        function showThumb(data){
			$llv("#" + data[0].id).css("background", "#000 url(" + data[0].thumbnail_large + ") center center no-repeat");
	    	// Test: Display Vimeo title
	    	<?php if (get_option('llv_opt_title') == true) { ?>
	    		$llv("#" + data[0].id).children().children('.titletext.vimeo').text(data[0].title);
	    	<?php } ?>	
        }

	    $llv(document).ready(function() {

	    	function doload_llv() {

	            function vimeoLoadingThumb(id){    
	                var url = "http://vimeo.com/api/v2/video/" + id + ".json?callback=showThumb";
	                  
	                var script = document.createElement( 'script' );
	                script.type = 'text/javascript';
	                script.src = url;

	                $llv("#" + id).prepend(script).prepend('<div style="height:'+(parseInt($llv("#" + id).css("height")))+'px;width:'+(parseInt($llv("#" + id).css("width")))+'px;" class="lazy-load-vimeo-div"><span class="titletext vimeo"></span></div>');
	            }  

				$llv(function vimeoCreateThumbProcess() {
					$llv(".preview-vimeo").each(function(  ) {
				        vid = $llv(this).attr('id');
				        $llv(vimeoLoadingThumb(vid));
					});
				});

				// Replace thumbnail with iframe
				$llv(function vimeoCreatePlayer() {
		            $llv('.preview-vimeo').on('click', function()
		            {
		            	vid = $llv(this).attr('id');
		                $llv(this).html('<iframe src="http://player.vimeo.com/video/' + vid + '?autoplay=1" style="height:'+(parseInt($llv("#"+vid).css("height")))+'px;width:100%" frameborder="0" webkitAllowFullScreen mozallowfullscreen autoPlay allowFullScreen></iframe>');
		            });
		        });
			}
			
			$llv(document).ready(doload_llv()).ajaxStop(function(){
				doload_llv();
			});
		})

	    </script>
	<?php }    
	add_action('wp_head', 'enable_lazyload_vimeo');


/***** Part 4: Add stylesheet and Custom CSS */
	function lazyload_youtube_style() {
		wp_register_style( 'lazy-load-style', plugins_url('style.css', __FILE__) );
		wp_enqueue_style( 'lazy-load-style' );
	}
	add_action( 'wp_enqueue_scripts', 'lazyload_youtube_style' );

	function lazyload_custom_css(){ ?>
		<style type="text/css">	
			<?php if (stripslashes(get_option('ll_opt_customcss')) != '') { ?>
				<?php echo stripslashes(get_option('ll_opt_customcss')); ?>
			<?php } ?>
		</style>
	<?php	}
	add_action( 'wp_head', 'lazyload_custom_css' );

/***** Part 5: Create options panel for admins (http://codex.wordpress.org/Creating_Options_Pages) */
	function ll_create_menu() {
		add_options_page('Lazy Load for Videos', 'Lazy Load for Videos', 'manage_options', 'lazyload.php', 'll_settings_page');
	}

	function register_ll_settings() {
		// Youtube
		register_setting( 'lly-settings-group', 'lly_opt' );
		register_setting( 'lly-settings-group', 'lly_opt_title' );

		// Vimeo
		register_setting( 'llv-settings-group', 'llv_opt' );
		register_setting( 'llv-settings-group', 'llv_opt_title' );

		// Other
		register_setting( 'll-settings-group', 'll_opt_customcss' );
		register_setting( 'll-settings-group', 'll_opt_customcss' );
	}

	function ll_settings_page() { ?>
	<div class="wrap">
	<h2>Lazy Load for Videos</h2>

    <?php $active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'lly_options'; ?>  

	<h2 class="nav-tab-wrapper">    
        <a href="?page=lazyload.php&tab=lly_options" class="nav-tab <?php echo $active_tab == 'lly_options' ? 'nav-tab-active' : ''; ?>">Youtube</a>
    	<a href="?page=lazyload.php&tab=llv_options" class="nav-tab <?php echo $active_tab == 'llv_options' ? 'nav-tab-active' : ''; ?>">Vimeo</a>
        <a href="?page=lazyload.php&tab=ll_options" class="nav-tab <?php echo $active_tab == 'll_options' ? 'nav-tab-active' : ''; ?>">Other</a>
    </h2>
	
	<form method="post" action="options.php">
		<table class="form-table">
		<?php

		// Tab "Youtube"
		if( $active_tab == 'lly_options' ) {  
         	settings_fields( 'lly-settings-group' );
	   		do_settings_sections( 'lly-settings-group' ); ?>
	        <tr valign="top">
		        <th scope="row">NOT use Lazy Load for Youtube</th>
		        <td>
					<input name="lly_opt" type="checkbox" value="1" <?php checked( '1', get_option( 'lly_opt' ) ); ?> /> <span>If checked, Lazy Load will not be used for <b>Youtube</b> videos.</span>
		        </td>
	        </tr>
	        <tr valign="top">
		        <th scope="row"><span style="color:#f60;">New!</span> Display Youtube title</th>
		        <td>
					<input name="lly_opt_title" type="checkbox" value="1" <?php checked( '1', get_option( 'lly_opt_title' ) ); ?> /> <span>If checked, the Youtube video title will be displayed on preview image.</span>
		        </td>
	        </tr>
	        <p><span style="color:#f60;">Important:</span> Changes will only affect new posts and posts you update afterwards. (Open the post editor and update/save your post again.)</p>
		<?php }

		// Tab "Vimeo"
		elseif( $active_tab == 'llv_options' ) {  
         	settings_fields( 'llv-settings-group' );
	   		do_settings_sections( 'llv-settings-group' ); ?>
	        <tr valign="top">
		        <th scope="row">NOT use Lazy Load for Vimeo</th>
		        <td>
					<input name="llv_opt" type="checkbox" value="1" <?php checked( '1', get_option( 'llv_opt' ) ); ?> /> <span>If checked, Lazy Load will not be used for <b>Vimeo</b> videos.</span>
		        </td>
	        </tr>
	        <tr valign="top">
		        <th scope="row"><span style="color:#f60;">New!</span> Display Vimeo title</th>
		        <td>
					<input name="llv_opt_title" type="checkbox" value="1" <?php checked( '1', get_option( 'llv_opt_title' ) ); ?> /> <span>If checked, the Vimeo video title will be displayed on preview image.</span>
		        </td>
	        </tr>
	        <p><span style="color:#f60;">Important:</span> Changes will only affect new posts and posts you update afterwards. (Open the post editor and update/save your post again.)</p>
		<?php }

		// Tab "Other"
        else {  
	    	settings_fields( 'll-settings-group' );	
	    	do_settings_sections( 'll-settings-group' ); ?>
	        <tr valign="top">
	        	<th scope="row">Custom CSS</th>
	        	<td>
	        		<textarea rows="14" cols="70" type="text" name="ll_opt_customcss"><?php echo get_option('ll_opt_customcss'); ?></textarea>
	        	</td>
	        </tr>
		<?php } ?>

		</table>
	    <?php submit_button(); ?>
	</form>

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
	<?php }
	add_action('admin_menu', 'll_create_menu');
	add_action('admin_init', 'register_ll_settings');

/***** Plugin by Kevin Weber || kevinw.de *****/
?>