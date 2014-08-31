/*
 * Lazy Load Youtube
 * by Kevin Weber (kevinw.de)
 */

var $lly = jQuery.noConflict();

// Classes
var classPreviewYoutube = 'preview-youtube';
  //var classPreviewYoutubeDot = '.' + classPreviewYoutube;
var classBranding = 'lazyload-info-icon';
  var classBrandingDot = '.' + classBranding;

// Helpers
var videoratio = 0.5625;
var thumbnailurl = '';


var $lly_o;
var setOptionsYoutube = function(options) {
  $lly_o = $lly.extend({
      theme: 'dark', // possible: dark, light
      colour: 'red', // possible: red, white
      controls: true,
      relations: true,
      playlist: '',
      videoseo: false,
      responsive: true,
      thumbnailquality: '0',
    },
    options);
};

$lly(document).ready(function() {

  var doload_lly = function() {

    $lly("a.lazy-load-youtube").each(function(index) {
      var that = this;

      var embedparms = $lly(this).attr("href").split("/embed/")[1];
      if (!embedparms) {
        embedparms = $lly(this).attr("href").split("://youtu.be/")[1];
      }
      if (!embedparms) {
        embedparms = $lly(this).attr("href").split("v=")[1].replace(/\&/, '?');
      }
      var youid = embedparms.split("?")[0].split("#")[0];
      var start = embedparms.match(/[#&]t=(\d+)s/);
      if (start) {
        start = start[1];
      } else {
        start = embedparms.match(/[#&]t=(\d+)m(\d+)s/);
        if (start) {
          start = parseInt(start[1]) * 60 + parseInt(start[2]);
        } else {
          start = embedparms.match(/[?&]start=(\d+)/);
          if (start) {
            start = start[1];
          }
        }
      }

      var emu = '//www.youtube.com/embed/' + embedparms;

      /*
       * Load plugin info
       */
      var loadPluginInfo = function() {
        return '<a class="' + classBranding + '" href="http://kevinw.de/lazyloadvideos" title="Lazy Load for Videos by Kevin Weber" target="_blank">i</a>';
      };

      /*
       * Create info element
       */
      var createPluginInfo = function() {
        if ($lly_o.displayBranding !== false) {
          // source = Video
          var source = $lly(that);
          // element = Plugin info element
          var element = $lly( loadPluginInfo() );
          // Prepend element to source
          source.before( element );
        }
      };

      createPluginInfo();

      var videoTitle = function() {
        if ( $lly(that).attr('video-title') !== undefined ) {
          return $lly(that).attr("video-title");
        }
        else if ( $lly(this).html() !== '' && $lly(this).html() !== undefined ) {
          return $lly(this).html();
        }
        else {
          return "";
        }
      };

      var youtubeUrl = function( id ) {
        return '//www.youtube.com/watch?v=' + id;
      };

      /*
       * Helpers to calculate dimensions
       */
      var getWidth = function( element ) {
        var calc = (parseInt(element.css("width")) - 4);
        return calc;   
      };
      var getHeight = function( element ) {
        var calc = 0;
        if ( $lly_o.responsive === false ) {
          calc = (parseInt(element.css("height")) - 4);
        }
        else {
          var width = getWidth( element );
          calc = Math.round( width * videoratio );
        }
        return calc; 
      };

      embedparms = embedparms.split("#")[0];
      if (start && embedparms.indexOf("start=") === -1) {
        embedparms += ((embedparms.indexOf("?") === -1) ? "?" : "&") + "start=" + start;
      }

      var itemprop_name = '';
      if ($lly_o.videoseo === true ) {
        itemprop_name = ' itemprop="name"';
      }

      if (embedparms.indexOf("showinfo=0") !== -1) {
        $lly(this).html('');
      } else {
        $lly(this).html('<div class="lazy-load-youtube-info"><span class="titletext youtube"'+itemprop_name+'>' + videoTitle() + '</span></div>');
      }

      $lly(this).prepend('<div style="height:' + getHeight($lly(this)) + 'px;width:' + getWidth($lly(this)) + 'px;" class="lazy-load-youtube-div"></div>');


      /*
       * Set thumbnail URL
       */
      var setThumbnailUrl = function( youid ) {
        var $url = "//i2.ytimg.com/vi/" + youid + "/" + $lly_o.thumbnailquality + ".jpg";
        
        thumbnailurl = $url;
      };   
      setThumbnailUrl(youid);
      
      /*
       * Get thumbnail URL
       */
      var getThumbnailUrl = function() {
        return thumbnailurl;
      };

      $lly(this).css("background", "#000 url(" + getThumbnailUrl() + ") center center no-repeat");

      if ($lly_o.videoseo === true) {
        $lly(that).append('<meta itemprop="contentLocation" content="'+ youtubeUrl( youid ) +'" />');
        $lly(that).append('<meta itemprop="embedUrl" content="'+ emu +'" />');
        $lly(this).append('<meta itemprop="thumbnail" content="'+ getThumbnailUrl() +'" />');
 
        $lly.getJSON('http://gdata.youtube.com/feeds/api/videos/'+youid+'?v=2&alt=jsonc&callback=?',function( data ){
            $lly(that).append('<meta itemprop="datePublished" content="'+ data.data.uploaded +'" />');
            $lly(that).append('<meta itemprop="duration" content="'+ data.data.duration +'" />');
            $lly(that).append('<meta itemprop="aggregateRating" content="'+ data.data.rating +'" />');
            // TODO: Retrieve and use even more data for Video SEO.
              // Get possible response data with http://www.jsoneditoronline.org/ and http://gdata.youtube.com/feeds/api/videos/pk99sSGF0YE?v=2&alt=jsonc
        });

      }

      $lly(this).attr("id", youid + index);
      $lly(this).attr("href", youtubeUrl( youid ) + (start ? "#t=" + start + "s" : ""));

      /*
       * Configure URL parameters
       */
      var theme = '';
      if ($lly_o.theme !== theme && $lly_o.theme !== undefined && $lly_o.theme !== 'dark') {
        theme = '&theme=' + $lly_o.theme;
      }
      var colour = '';
      if ($lly_o.colour !== colour && $lly_o.colour !== undefined && $lly_o.colour !== 'red') {
        colour = '&color=' + $lly_o.colour;
      }
      var relations = '';
      if (!$lly_o.relations) {
        relations = '&rel=0';
      }
      var controls = '';
      if (!$lly_o.controls) {
        controls = '&controls=0';
      }
      var playlist = '';
      if ($lly_o.playlist !== playlist && $lly_o.playlist !== undefined) {
        playlist = '&playlist=' + $lly_o.playlist;
      }

      /*
       * Generate URL
       */
      emu += ((emu.indexOf("?") === -1) ? "?" : "&") + "autoplay=1" + theme + colour + controls + relations + playlist;

      /*
       * Generate iFrame
       */
      var videoFrame = '<iframe width="' + parseInt($lly(this).css("width")) + '" height="' + parseInt($lly(this).css("height")) + '" style="vertical-align:top;" src="' + emu + '" frameborder="0" allowfullscreen></iframe>';

      /*
       * Register "onclick" event handler
       */
      $lly( this ).on( "click", function() {

        removePlayerControls(this);
        removeBranding(this);

        $lly('#' + youid + index).replaceWith( videoFrame );
        if (typeof responsiveVideos.resize === 'function' && $lly_o.responsive === true) { 
          responsiveVideos.resize(); 
        }
        return false;
      });

      var removePlayerControls = function( element ) {
        $lly(element).removeClass(classPreviewYoutube);
      };
      var removeBranding = function( element ) {
        $lly(element).prev( classBrandingDot ).remove();
      };

    });

  };

  /*
   * Use ajaxStop function to prevent plugin from breaking when another plugin uses Ajax
   */
  $lly(document).ready(doload_lly()).ajaxStop(function() {
    doload_lly();
  });

  /*
   * Prevent users from removing branding // YOU'RE NOT ALLOWED TO EDIT THE FOLLOWING LINES OF CODE
   */
  var displayBranding = function() {
    if ($lly_o.displayBranding !== false) {
      $lly(classBrandingDot).css({
        'display': 'block',
        'visibility': 'visible',
      });
    }
  };
  displayBranding();



  /*
   * Ensure that a handler is run before any other registered handlers,
   * independent of the order in which they were bound
   * As seen on http://stackoverflow.com/questions/2360655/jquery-event-handlers-always-execute-in-order-they-were-bound-any-way-around-t
   * and on https://gist.github.com/infostreams/6540654
   */
  $lly.fn.bindFirst = function(which, handler) {
        // ensures a handler is run before any other registered handlers, 
        // independent of the order in which they were bound
        var $el = $lly(this);
        $el.unbind(which, handler);
        $el.bind(which, handler);
   
        var events = $lly._data($el[0]).events;
        var registered = events[which];
        registered.unshift(registered.pop());
   
        events[which] = registered;
      };

  /*
   * The following code bases on "Responsive Video Embeds" by Kevin Leary, www.kevinleary.net, WordPress development in Boston, MA
   */
  var responsiveVideos = {

    config: {
      container: $lly( '.container-lazyload' ),
      selector: 'object, embed, iframe, .preview-lazyload, .lazy-load-youtube-div, .lazy-load-vimeo-div'
    },

    init: function( config ) {
      if ( responsiveVideos.config.container.length > 0 ) {
        $lly( window ).on( 'resize', responsiveVideos.resize );
        // Use bindFirst() to ensure that other plugins like Inline Comments work correctly (in case they depend on the video heights)
        $lly( window ).bindFirst( 'load', function() { responsiveVideos.resize(); } );
      }
    },

    resize: function() {
      $lly( responsiveVideos.config.selector, responsiveVideos.config.container ).each( function () {

        var $this = $lly( this );
        var width = $this.parent().width();
        var height = Math.round( width * videoratio );

        $this.attr( 'height', height );
        $this.attr( 'width', width );
        $this.css({
            'height': height,
            'width': width,
          });

      });
    },

  };

  if (typeof responsiveVideos.init === 'function' && $lly_o.responsive === true ) { 
    responsiveVideos.init();
  }



});