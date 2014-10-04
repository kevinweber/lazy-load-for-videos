/*
 * Lazy Load Youtube
 * by Kevin Weber (kevinw.de)
 */

jQuery.noConflict();
(function( lazyload_youtube, $, undefined ) {

  // Classes
  var classPreviewYoutube = 'preview-youtube';
    //var classPreviewYoutubeDot = '.' + classPreviewYoutube;
  var classBranding = 'lazyload-info-icon';
    var classBrandingDot = '.' + classBranding;

  // Helpers
  var videoratio = 0.5625;
  var thumbnailurl = '';

  lazyload_youtube.init = function( options ) {
    setOptionsYoutube( options );

    /*
     * Use ajaxStop function to prevent plugin from breaking when another plugin uses Ajax
     */
    $(document).ready(doload_lly()).ajaxStop(function() {
      doload_lly();
    });

    if (typeof responsiveVideos.init === 'function' && $_o.responsive === true ) { 
      responsiveVideos.init();
    }

  };

  var $_o;
  var setOptionsYoutube = function(options) {
    $_o = $.extend({
        theme: 'dark', // possible: dark, light
        colour: 'red', // possible: red, white
        controls: true,
        relations: true,
        buttonstyle: '',
        preroll: '',
        postroll: '',
        videoseo: false,
        responsive: true,
        thumbnailquality: '0',
      },
      options);
  };

  var doload_lly = function() {

    $("a.lazy-load-youtube").each(function(index) {
      var that = this;

      /*
       * Load parameters from user's original Youtube URL
       */
      var load_embedparms = function() {
        var embedparms = $(that).attr("href").split("/embed/")[1];
        if (!embedparms) {
          embedparms = $(that).attr("href").split("://youtu.be/")[1];
        }
        if (!embedparms) {
          embedparms = $(that).attr("href").split("v=")[1].replace(/\&/, '?');
        }
        return embedparms;
      };
      var embedparms = load_embedparms();

      /*
       * Load Youtube ID
       */
      var loadYouId = function() {
        return embedparms.split("?")[0].split("#")[0];
      };
      var youid = loadYouId();

      var loadYouIdPreroll = function() {
        var preroll = '';
        if ($_o.preroll !== preroll && $_o.preroll !== undefined) {
          return $_o.preroll;
        }
        else {
          // Fallback when no preroll ID should be loaded
          return embedparms;
        }
      };
      var preroll = loadYouIdPreroll();
 
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

      var emu = '//www.youtube.com/embed/' + loadYouIdPreroll();

      /*
       * Load plugin info
       */
      var loadPluginInfo = function() {
        return '<a class="' + classBranding + '" href="http://kevinw.de/lazy-load-videos/" title="Lazy Load for Videos by Kevin Weber" target="_blank">i</a>';
      };

      /*
       * Create info element
       */
      var createPluginInfo = function() {
        if ($_o.displayBranding !== false) {
          // source = Video
          var source = $(that);
          // element = Plugin info element
          var element = $( loadPluginInfo() );
          // Prepend element to source
          source.before( element );
        }
      };

      createPluginInfo();

      var videoTitle = function() {
        if ( $(that).attr('video-title') !== undefined ) {
          return $(that).attr("video-title");
        }
        else if ( $(this).html() !== '' && $(this).html() !== undefined ) {
          return $(this).html();
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
        if ( $_o.responsive === false ) {
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
      if ($_o.videoseo === true ) {
        itemprop_name = ' itemprop="name"';
      }

      if (embedparms.indexOf("showinfo=0") !== -1) {
        $(this).html('');
      } else {
        $(this).html('<div class="lazy-load-youtube-info"><span class="titletext youtube"'+itemprop_name+'>' + videoTitle() + '</span></div>');
      }

      $(this).prepend('<div style="height:' + getHeight($(this)) + 'px;width:' + getWidth($(this)) + 'px;" class="lazy-load-youtube-div"></div>').addClass($_o.buttonstyle);


      /*
       * Set thumbnail URL
       */
      var setThumbnailUrl = function( youid ) {
        var $url = "//i2.ytimg.com/vi/" + youid + "/" + $_o.thumbnailquality + ".jpg";
        
        thumbnailurl = $url;
      };   
      setThumbnailUrl(youid);
      
      /*
       * Get thumbnail URL
       */
      var getThumbnailUrl = function() {
        return thumbnailurl;
      };

      $(this).css("background", "#000 url(" + getThumbnailUrl() + ") center center no-repeat");

      if ($_o.videoseo === true) {
        $(that).append('<meta itemprop="contentLocation" content="'+ youtubeUrl( youid ) +'" />');
        $(that).append('<meta itemprop="embedUrl" content="'+ emu +'" />');
        $(this).append('<meta itemprop="thumbnail" content="'+ getThumbnailUrl() +'" />');
 
        $.getJSON('http://gdata.youtube.com/feeds/api/videos/'+youid+'?v=2&alt=jsonc&callback=?',function( data ){
            $(that).append('<meta itemprop="datePublished" content="'+ data.data.uploaded +'" />');
            $(that).append('<meta itemprop="duration" content="'+ data.data.duration +'" />');
            $(that).append('<meta itemprop="aggregateRating" content="'+ data.data.rating +'" />');
            // TODO: Retrieve and use even more data for Video SEO.
              // Get possible response data with http://www.jsoneditoronline.org/ and http://gdata.youtube.com/feeds/api/videos/pk99sSGF0YE?v=2&alt=jsonc
        });

      }

      $(this).attr("id", youid + index);
      $(this).attr("href", youtubeUrl( youid ) + (start ? "#t=" + start + "s" : ""));

      /*
       * Configure URL parameters
       */
      var theme = '';
      if ($_o.theme !== theme && $_o.theme !== undefined && $_o.theme !== 'dark') {
        theme = '&theme=' + $_o.theme;
      }
      var colour = '';
      if ($_o.colour !== colour && $_o.colour !== undefined && $_o.colour !== 'red') {
        colour = '&color=' + $_o.colour;
      }
      var relations = '';
      if (!$_o.relations) {
        relations = '&rel=0';
      }
      var controls = '';
      if (!$_o.controls) {
        controls = '&controls=0';
      }

      /*
       * Generate Youtube URL parameter 'playlist'
       */
      if (preroll !== youid) {
        preroll = youid + ',';
      }
      else {
        preroll = '';
      }
      var postroll = '';
      if ($_o.postroll !== postroll && $_o.postroll !== undefined) {
        postroll = $_o.postroll;
      }
      var playlist = '';
      if ( ( preroll !== '' ) || ( postroll !== '' ) ) {
        playlist = '&playlist=' + preroll + postroll;
      }

      /*
       * Generate URL
       */
      emu += ((emu.indexOf("?") === -1) ? "?" : "&") + "autoplay=1" + theme + colour + controls + relations + playlist;

      /*
       * Generate iFrame
       */
      var videoFrame = '<iframe width="' + parseInt($(this).css("width")) + '" height="' + parseInt($(this).css("height")) + '" style="vertical-align:top;" src="' + emu + '" frameborder="0" allowfullscreen></iframe>';

      /*
       * Register "onclick" event handler
       */
      $( this ).on( "click", function() {

        removePlayerControls(this);
        removeBranding(this);

        $('#' + youid + index).replaceWith( videoFrame );
        if (typeof responsiveVideos.resize === 'function' && $_o.responsive === true) { 
          responsiveVideos.resize(); 
        }
        return false;
      });

      var removePlayerControls = function( element ) {
        $(element).removeClass(classPreviewYoutube);
      };
      var removeBranding = function( element ) {
        $(element).prev( classBrandingDot ).remove();
      };

    });

  };


  /*
   * Ensure that a handler is run before any other registered handlers,
   * independent of the order in which they were bound
   * As seen on http://stackoverflow.com/questions/2360655/jquery-event-handlers-always-execute-in-order-they-were-bound-any-way-around-t
   * and on https://gist.github.com/infostreams/6540654
   */
  $.fn.bindFirst = function(which, handler) {
        // ensures a handler is run before any other registered handlers, 
        // independent of the order in which they were bound
        var $el = $(this);
        $el.unbind(which, handler);
        $el.bind(which, handler);
   
        var events = $._data($el[0]).events;
        var registered = events[which];
        registered.unshift(registered.pop());
   
        events[which] = registered;
      };

  /*
   * The following code bases on "Responsive Video Embeds" by Kevin Leary, www.kevinleary.net, WordPress development in Boston, MA
   */
  var responsiveVideos = {

    config: {
      container: $( '.container-lazyload' ),
      selector: 'object, embed, iframe, .preview-lazyload, .lazy-load-youtube-div, .lazy-load-vimeo-div'
    },

    init: function() {
      if ( responsiveVideos.config.container.length > 0 ) {
        $( window ).on( 'resize', responsiveVideos.resize );
        // Use bindFirst() to ensure that other plugins like Inline Comments work correctly (in case they depend on the video heights)
        $( window ).bindFirst( 'load', function() { responsiveVideos.resize(); } );
      }
    },

    resize: function() {
      $( responsiveVideos.config.selector, responsiveVideos.config.container ).each( function () {

        var $this = $( this );
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

}( window.lazyload_youtube = window.lazyload_youtube || {}, jQuery ));