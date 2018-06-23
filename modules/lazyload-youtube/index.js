/*global lazyload_video_settings */

/*
 * Lazy Load Youtube
 * by Kevin Weber (kevinw.de)
 */
(function( lazyload_youtube, $, undefined ) {

  // Classes
  var classPreviewYoutube = 'preview-youtube';
    var classPreviewYoutubeDot = '.' + classPreviewYoutube;
  var classBranding = 'lazyload-info-icon';
    var classBrandingDot = '.' + classBranding;
  var classNotLoaded = 'js-lazyload--not-loaded';

  // Helpers
  var videoratio = 0.5625;
  var thumbnailurl = '';

  function markInitialized() {
    $(classPreviewYoutubeDot).parent().removeClass(classNotLoaded);
  }

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
    } else {
      markInitialized();
    }

    if (typeof $_o.callback === 'function') {
        $_o.callback();
    }

  };

  var $_o;
  var setOptionsYoutube = function(options) {
    $_o = $.extend({
        theme: 'dark', // possible: dark, light
        colour: 'red', // possible: red, white
        controls: true,
        loadpolicy: true,
        showinfo: true,
        relations: true,
        buttonstyle: '',
        preroll: '',
        postroll: '',
        videoseo: false,
        responsive: true,
        thumbnailquality: '0',
        displaybranding: false,
        loadthumbnail: true,
        callback: null,
      },
      options);
  };

  var doload_lly = function() {

    $("a.lazy-load-youtube").each(function(index) {
      var $that = $(this);
      var $thatHref = $that.attr("href");
      var embedparms;
      var preroll = '';

      /*
       * Load parameters from user's original Youtube URL
       */
      (function setEmbedParams() {
        embedparms = $thatHref.split('/embed/')[1];
        if (!embedparms) {
          embedparms = $thatHref.split('://youtu.be/')[1];
        }
        if (!embedparms) {
          embedparms = $thatHref.split('v=')[1].replace(/&/, '?');
        }
      })();

      /*
       * Load Youtube ID
       */
      var youid = embedparms.split('?')[0].split('#')[0];

      (function setYouIdPreroll() {
        if ($_o.preroll !== undefined && $_o.preroll !== preroll) {
          preroll = $_o.preroll;
        }
        else {
          // Fallback when no preroll ID should be loaded
          preroll = embedparms;
        }
      })();

      var emu = '//www.youtube.com/embed/' + preroll;

      /*
       * Load plugin info
       */
      var loadPluginInfo = function() {
        return '<a class="' + classBranding + '" href="//kevinw.de/lazy-load-videos/" title="Lazy Load for Videos by Kevin Weber" target="_blank">i</a>';
      };

      /*
       * Create info element
       */
      var createPluginInfo = function() {
        if (
            ( $_o.displaybranding === true ) &&
            ( $that.siblings(classBrandingDot).length === 0 ) // This prevents the site from creating unnecessary duplicate brandings
          )
        {
          // source = Video
          var source = $that;
          // element = Plugin info element
          var element = $( loadPluginInfo() );
          // Prepend element to source
          source.before( element );
        }
      };

      createPluginInfo();

      var videoTitle = function() {
        // Since v2.3: "video-title" is no longer used in our code but we keep it here because several blogs still have posts/pages cached with the video-title attribute (instead of the new data-video-title)
        if ( $that.attr('video-title') !== undefined ) {
          return $that.attr('video-title');
        } else if ( $that.attr('data-video-title') !== undefined ) {
          return $that.attr('data-video-title');
        } else if ( $that.html() !== undefined && $that.html() !== '' ) {
          return $that.html();
        } else {
          return '';
        }
      };

      var youtubeUrl = function( id ) {
        return '//www.youtube.com/watch?v=' + id;
      };

      /*
       * Helpers to calculate dimensions
       */
      var getWidth = function( element ) {
        var calc = (parseInt(element.css('width')) - 4);
        return calc;
      };
      var getHeight = function( element ) {
        var calc = 0;
        if ( $_o.responsive === false ) {
          calc = (parseInt(element.css('height')) - 4);
        }
        else {
          var width = getWidth( element );
          calc = Math.round( width * videoratio );
        }
        return calc;
      };

      var start = 0;
      var time_factors = [3600, 60, 1]; // h, m, s
      var start_match = embedparms.match(/[#&?]t=(?:(\d+)(?:h))?(?:(\d+)(?:m))?(?:(\d+)(?:s))?/);
      if (start_match) {
        for (var s=1; s < start_match.length; s++) {
          if (typeof start_match[s] !== 'undefined') {
            start += parseInt(start_match[s])*time_factors[s-1];
          }
        }
      }
      if (start === 0) {
        start_match = embedparms.match(/[#&?](?:t|start)=(\d+)/);
        if (start_match) {
          start = start_match[1];
        }
      }

      embedparms = embedparms.split('#')[0];
      var embedstart = '';
      if (start && start !== 0 && embedparms.indexOf('start=') === -1) {
        embedstart = ((embedparms.indexOf('?') === -1) ? '?' : '&') + 'start=' + start;
      }

      var itemprop_name = '';
      if ($_o.videoseo === true ) {
        itemprop_name = ' itemprop="name"';
      }

      if (embedparms.indexOf('showinfo=0') !== -1) {
        $that.html('');
      } else {
        $that.html('<div class="lazy-load-youtube-info"><span class="titletext youtube"'+itemprop_name+'>' + videoTitle() + '</span></div>');
      }

      $that.prepend('<div style="height:' + getHeight($that) + 'px;width:' + getWidth($that) + 'px;" class="lazy-load-youtube-div"></div>').addClass($_o.buttonstyle);


      /*
       * Set thumbnail URL
       */
      var setThumbnailUrl = function( youid ) {
        var $url = '//i2.ytimg.com/vi/' + youid + '/' + $_o.thumbnailquality + '.jpg';

        thumbnailurl = $url;
      };
      setThumbnailUrl(youid);

      /*
       * Get thumbnail URL
       */
      var getThumbnailUrl = function() {
        return thumbnailurl;
      };

      var setBackgroundImg = function( el ) {
        var src = getThumbnailUrl(),
          img = $('<img style="display:none" src="' + src + '"/>');
        img.load(function() {
            // If the max resolution thumbnail is not available, fall back to smaller size.
            // But note that we'll still see an 404 error in the console in this case.
            if (img.width() === 120) {
              src = src.replace('maxresdefault', '0');
            }
            if (el.css('background-image') === 'none') {
              el.css('background-image', 'url(' + src + ')');
              el.css('background-color', '#000');
              el.css('background-position', 'center center');
              el.css('background-repeat', 'no-repeat');
            }
            img.remove();
        });
        $('body').append(img);
      };

      if ($_o.loadthumbnail) {
        setBackgroundImg($that);
      }

      if ($_o.videoseo === true) {
        $that.append('<meta itemprop="contentLocation" content="'+ youtubeUrl( youid ) +'" />');
        $that.append('<meta itemprop="embedUrl" content="'+ emu +'" />');
        $that.append('<meta itemprop="thumbnail" content="'+ getThumbnailUrl() +'" />');

        $.getJSON('//gdata.youtube.com/feeds/api/videos/'+youid+'?v=2&alt=jsonc&callback=?',function( data ){
            $that.append('<meta itemprop="datePublished" content="'+ data.data.uploaded +'" />');
            $that.append('<meta itemprop="duration" content="'+ data.data.duration +'" />');
            $that.append('<meta itemprop="aggregateRating" content="'+ data.data.rating +'" />');
            // TODO: Retrieve and use even more data for Video SEO.
              // Get possible response data with //www.jsoneditoronline.org/ and //gdata.youtube.com/feeds/api/videos/pk99sSGF0YE?v=2&alt=jsonc
        });

      }

      $that.attr('id', youid + index);
      $that.attr('href', youtubeUrl( youid ) + (start ? '#t=' + start + 's' : ''));


      (function generateUrl() {
        var theme = '',
            colour = '',
            postroll = '',
            playlist = '',
            showinfo, relations, controls, loadpolicy;

        /*
         * Configure URL parameters
         */
        if ($_o.theme !== undefined && $_o.theme !== theme && $_o.theme !== 'dark') {
          theme = '&theme=' + $_o.theme;
        }
        if ($_o.colour !== undefined && $_o.colour !== colour && $_o.colour !== 'red') {
          colour = '&color=' + $_o.colour;
        }
        showinfo = !$_o.showinfo ? '&showinfo=0' : '';
        relations = !$_o.relations ? '&rel=0' : '';
        controls = !$_o.controls ? '&controls=0' : '';
        loadpolicy = !$_o.loadpolicy ? '&iv_load_policy=3' : '';

        /*
         * Configure URL parameter 'playlist'
         */
        if (preroll !== youid) {
          preroll = youid + ',';
        } else {
          preroll = '';
        }
        if ($_o.postroll !== undefined && $_o.postroll !== postroll) {
          postroll = $_o.postroll;
        }
        if ( ( preroll !== '' ) || ( postroll !== '' ) ) {
          playlist = '&playlist=' + preroll + postroll;
        }

        /*
         * Generate URL
         */
        emu += ((emu.indexOf('?') === -1) ? '?' : '&') + 'autoplay=1' + theme + colour + controls + loadpolicy + showinfo + relations + playlist + embedstart;
      })();


      /*
       * Generate iFrame
       */
      var videoFrame = '<iframe width="' + parseInt($that.css("width")) + '" height="' + parseInt($that.css("height")) + '" style="vertical-align:top;" src="' + emu + '" frameborder="0" allowfullscreen></iframe>';

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
   * As seen on //stackoverflow.com/questions/2360655/jquery-event-handlers-always-execute-in-order-they-were-bound-any-way-around-t
   * and on //gist.github.com/infostreams/6540654
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
        $( window ).on( 'load', function() {
          responsiveVideos.resize();
          markInitialized();
        } );
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

  $(function() {
    lazyload_youtube.init(lazyload_video_settings.youtube);
  });

//  /*
//   * Speed test
//   * Exemplary usage:
//  // var setEmbedParamsTest = new SpeedTest(setEmbedParams, null, 500000);
//  // setEmbedParamsTest.startTest();
//  */
//  function SpeedTest( testImplement, testParams, repititions ) {
//    this.testImplement = testImplement;
//    this.testParams = testParams;
//    this.repititions = repititions || 10000;
//    this.average = 0;
//  }
//
//  SpeedTest.prototype = {
//    startTest: function() {
//      var beginTime, endTime, sumTimes = 0;
//      for (var i = 0, x = this.repititions; i < x; i++) {
//        beginTime = +new Date(); // Use "+" to return date in ms
//        this.testImplement(this.testParams);
//        endTime = +new Date();
//        sumTimes += endTime - beginTime;
//      }
//      this.average = sumTimes / this.repititions;
//      return console.log("Average execution across " +
//                          this.repititions + ": " +
//                          this.average);
//    }
//  };

}( window.lazyload_youtube = window.lazyload_youtube || {}, jQuery ));
