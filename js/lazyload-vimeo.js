/*
 * Lazy Load Vimeo
 * by Kevin Weber (kevinw.de)
 */

var $llv = jQuery.noConflict();

// Classes
var classPreviewVimeo = 'preview-vimeo';
  var classPreviewVimeoDot = '.' + classPreviewVimeo;
var classBranding = 'lazyload-info-icon';
  var classBrandingDot = '.' + classBranding;

// Helpers
var videoratio = 0.5625;


var $llv_o;
var setOptionsVimeo = function(options) {
  $llv_o = $llv.extend({
      buttonstyle: '',
      playercolour: '',
      videoseo: false,
      responsive: true,
    },
    options);
};

$llv(document).ready(function() {


  function doload_llv() {
    vimeoCreateThumbProcess();

    createPluginInfo();

    // Replace thumbnail with iframe
    vimeoCreatePlayer();
  }

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
        if ($llv_o.displayBranding !== false) {
          // source = Video
          var source = $llv( classPreviewVimeoDot );
          // element = Plugin info element
          var element = $llv( loadPluginInfo() );
          // Prepend element to source
          source.before( element );
        }
      };


  var vimeoCreatePlayer = function() {
    $llv(classPreviewVimeoDot).on('click', function() {
      var vid = getAttrId(this);

      removePlayerControls(this);
      removeBranding(this);
      
      var playercolour = '';
      if ($llv_o.playercolour !== playercolour) {
        $llv_o.playercolour = filterDotHash($llv_o.playercolour);
        playercolour = '&color=' + $llv_o.playercolour;
      }

      $llv(this).html('<iframe src="' + vimeoUrl( vid ) + '?autoplay=1' + playercolour + '" style="height:' + (parseInt($llv("#" + vid).css("height"))) + 'px;width:100%" frameborder="0" webkitAllowFullScreen mozallowfullscreen autoPlay allowFullScreen></iframe>');
      if (typeof responsiveVideos.resize === 'function' && $llv_o.responsive === true) { 
        responsiveVideos.resize(); 
      }
    });
  };

  var removePlayerControls = function( element ) {
      $llv(element).removeClass(classPreviewVimeo);
  };
  var removeBranding = function( element ) {
    $llv(element).prev(classBrandingDot).remove();
  };

  var vimeoUrl = function( id ) {
    return '//player.vimeo.com/video/' + id;
  };

  // Remove dots and hashs from a string
  var filterDotHash = function(variable) {
    var filterdothash = variable.toString().replace(/[.#]/g, "");
    return filterdothash;
  };

  var vimeoCreateThumbProcess = function() {
    $llv(classPreviewVimeoDot).each(function() {
      var vid = getAttrId(this);
      vimeoLoadingThumb(vid);
    });
  };

  var vimeoLoadingThumb = function(id) {
    var url = vimeoCallbackUrl(id) + ".json?callback=showThumb";

    var script = document.createElement('script');
    script.type = 'text/javascript';
    script.src = url;

    var itemprop_name = '';
    if ($llv_o.videoseo === true ) {
      itemprop_name = ' itemprop="name"';
    }

    $llv("#" + id).prepend(script).prepend('<div style="height:' + (parseInt($llv("#" + id).css("height"))) + 'px;width:' + (parseInt($llv("#" + id).css("width"))) + 'px;" class="lazy-load-vimeo-div"><span class="titletext vimeo"'+itemprop_name+'></span></div>').addClass($llv_o.buttonstyle);

    vimeoVideoSeo( id );
  };

  var vimeoVideoSeo = function( id ) {
    if ($llv_o.videoseo === true) {

      $llv.getJSON( vimeoCallbackUrl( id ) + '?callback=?', {format: "json"}, function(data) {

        $llv("#" + id).append('<meta itemprop="contentLocation" content="' + data[0].url +'" />');
        $llv("#" + id).append('<meta itemprop="embedUrl" content="' + vimeoUrl(id) +'" />');
        $llv("#" + id).append('<meta itemprop="thumbnail" content="'+ data[0].thumbnail_large +'" />');
        $llv("#" + id).append('<meta itemprop="datePublished" content="'+ data[0].upload_date +'" />');
        $llv("#" + id).append('<meta itemprop="duration" content="'+ data[0].duration +'" />');
        $llv("#" + id).append('<meta itemprop="aggregateRating" content="'+ data.data.rating +'" />');
        // TODO: Retrieve and use even more data for Video SEO. Possible data: https://developer.vimeo.com/apis/simple#response-data
      
      });

    }
  };

  var vimeoCallbackUrl = function( id ) {
    return '//vimeo.com/api/v2/video/' + id + '.json';
  };

  var getAttrId = function(element) {
    var vid = $llv(element).attr('id');
    return vid;
  };

  /*
   * Use ajaxStop function to prevent plugin from breaking when another plugin uses Ajax
   */
  $llv(document).ready(doload_llv()).ajaxStop(function() {
    doload_llv();
  });


  /*
   * Ensure that a handler is run before any other registered handlers,
   * independent of the order in which they were bound
   * As seen on http://stackoverflow.com/questions/2360655/jquery-event-handlers-always-execute-in-order-they-were-bound-any-way-around-t
   * and on https://gist.github.com/infostreams/6540654
   */
  $llv.fn.bindFirst = function(which, handler) {
        // ensures a handler is run before any other registered handlers, 
        // independent of the order in which they were bound
        var $el = $llv(this);
        $el.unbind(which, handler);
        $el.bind(which, handler);
   
        var events = $llv._data($el[0]).events;
        var registered = events[which];
        registered.unshift(registered.pop());
   
        events[which] = registered;
      };

  /*
   * The following code bases on "Responsive Video Embeds" by Kevin Leary, www.kevinleary.net, WordPress development in Boston, MA
   */
  var responsiveVideos = {

    config: {
      container: $llv( '.container-lazyload' ),
      selector: 'object, embed, iframe, .preview-lazyload, .lazy-load-youtube-div, .lazy-load-vimeo-div'
    },

    init: function() {
      if ( responsiveVideos.config.container.length > 0 ) {
        $llv( window ).on( 'resize', responsiveVideos.resize );
        // Use bindFirst() to ensure that other plugins like Inline Comments work correctly (in case they depend on the video heights)
        $llv( window ).bindFirst( 'load', function() { responsiveVideos.resize(); } );
      }
    },

    resize: function() {
      $llv( responsiveVideos.config.selector, responsiveVideos.config.container ).each( function () {

        var $this = $llv( this );
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

  if (typeof responsiveVideos.init === 'function' && $llv_o.responsive === true ) { 
    responsiveVideos.init();
  }



});