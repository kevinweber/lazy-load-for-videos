/*
 * Lazy Load Vimeo
 * by Kevin Weber (www.kweber.com)
 */

(function (lazyload_vimeo, $) {
  window.showThumb = function showThumb(data) {
    var relevantData = data[0];

    if (lazyload_video_settings.vimeo.loadthumbnail) {
      $('#' + relevantData.id)
        .css('background-image', 'url(' + relevantData.thumbnail_large + ')')
        .css('background-color', '#000')
        .css('background-position', 'center center')
        .css('background-repeat', 'no-repeat');
    }
  };

  // Classes
  var classPreviewVimeo = 'preview-vimeo';
  var classPreviewVimeoDot = '.' + classPreviewVimeo;
  var classBranding = 'lazyload-info-icon';
  var classBrandingDot = '.' + classBranding;
  var classNotLoaded = 'js-lazyload--not-loaded';

  // Helpers
  var videoratio = 0.5625;

  function markInitialized() {
    $(classPreviewVimeoDot).parent().removeClass(classNotLoaded);
  }

  lazyload_vimeo.init = function (options) {
    setOptionsVimeo(options);

    /*
     * Use ajaxStop function to prevent plugin from breaking when another plugin uses Ajax
     */
    $(document).ready(doload_llv()).ajaxStop(function () {
      doload_llv();
    });

    if (typeof responsiveVideos.init === 'function' && $_o.responsive === true) {
      responsiveVideos.init();
    } else {
      markInitialized();
    }

    if (typeof $_o.callback === 'function') {
      $_o.callback();
    }

  };

  var $_o;
  var setOptionsVimeo = function (options) {
    $_o = $.extend({
        buttonstyle: '',
        playercolour: '',
        videoseo: false,
        responsive: true,
        displayBranding: false,
        loadthumbnail: true,
        callback: null,
      },
      options);
  };

  function doload_llv() {
    vimeoCreateThumbProcess();

    createPluginInfo();

    // Replace thumbnail with iframe
    vimeoCreatePlayer();
  }

  /*
   * Load plugin info
   */
  var loadPluginInfo = function () {
    return '<a class="' + classBranding + '" href="https://www.kweber.com/lazy-load-videos/" title="Lazy Load for Videos by Kevin Weber" target="_blank">i</a>';
  };

  /*
   * Create info element
   */
  var createPluginInfo = function () {
    if (
      ($_o.displayBranding === true) &&
      ($(classPreviewVimeoDot).siblings(classBrandingDot).length === 0) // This prevents the site from creating unnecessary duplicate brandings
    ) {
      // source = Video
      var source = $(classPreviewVimeoDot);
      // element = Plugin info element
      var element = $(loadPluginInfo());
      // Prepend element to source
      source.before(element);
    }
  };


  var vimeoCreatePlayer = function () {
    $(classPreviewVimeoDot).on('click', function (event) {
      event.preventDefault();
      
      var vid = $(this).attr('id');

      removePlayerControls(this);
      removeBranding(this);

      var playercolour = '';
      if ($_o.playercolour !== playercolour) {
        $_o.playercolour = filterDotHash($_o.playercolour);
        playercolour = '&color=' + $_o.playercolour;
      }

      $(this).replaceWith('<iframe src="' + vimeoUrl(vid) + '?autoplay=1' + playercolour + '" style="height:' + (parseInt($("#" + vid).css("height"))) + 'px;width:100%" frameborder="0" webkitAllowFullScreen mozallowfullscreen autoPlay allowFullScreen></iframe>');
      if (typeof responsiveVideos.resize === 'function' && $_o.responsive === true) {
        responsiveVideos.resize();
      }
    });
  };

  var removePlayerControls = function (element) {
    $(element).removeClass(classPreviewVimeo);
  };
  var removeBranding = function (element) {
    $(element).prev(classBrandingDot).remove();
  };

  var vimeoUrl = function (id) {
    return 'https://player.vimeo.com/video/' + id;
  };

  // Remove dots and hashs from a string
  var filterDotHash = function (variable) {
    var filterdothash = variable.toString().replace(/[.#]/g, "");
    return filterdothash;
  };

  var vimeoCreateThumbProcess = function () {
    $(classPreviewVimeoDot).each(function () {
      var $this = $(this);
      var vid = $this.attr('id');

      $this.empty(); // Remove no longer needed title (title is necessary for preview in text editor)

      vimeoLoadingThumb($this, vid);
    });
  };

  var vimeoLoadingThumb = function ($container, id) {
    var script;

    if (lazyload_video_settings.vimeo.loadthumbnail) {
      script = document.createElement('script');
      script.type = 'text/javascript';
      script.src = vimeoCallbackUrl(id) + ".json?callback=showThumb";

      $container.after(script);
    }

    var itemprop_name = '';
    if ($_o.videoseo === true) {
      itemprop_name = ' itemprop="name"';
    }

    var info = '';
    if (lazyload_video_settings.vimeo.show_title) {
      var videoTitle = $container.attr('data-video-title');
      info = '<div aria-hidden="true" class="lazy-load-info"><span class="titletext vimeo"' + itemprop_name + ' >' + videoTitle + '</span></div>';
    }

    $container
        .prepend(info)
        .prepend('<div aria-hidden="true" style="height:' + (parseInt($("#" + id).css("height"))) + 'px;width:' + (parseInt($("#" + id).css("width"))) + 'px;" class="lazy-load-div"></div>')
        .addClass($_o.buttonstyle);

    vimeoVideoSeo(id);
  };

  var vimeoVideoSeo = function (id) {
    if ($_o.videoseo === true) {
      $.getJSON(vimeoCallbackUrl(id) + '?callback=?', {
        format: 'json'
      }, function (data) {
        var relevantData = data[0];

        $("#" + id)
          .append('<meta itemprop="contentLocation" content="' + relevantData.url + '" />')
          .append('<meta itemprop="embedUrl" content="' + vimeoUrl(id) + '" />')
          .append('<meta itemprop="thumbnail" content="' + relevantData.thumbnail_large + '" />')
          .append('<meta itemprop="datePublished" content="' + relevantData.upload_date + '" />')
          .append('<meta itemprop="duration" content="' + relevantData.duration + '" />')
          .append('<meta itemprop="aggregateRating" content="' + data.data.rating + '" />');
        // TODO: Retrieve and use even more data for Video SEO. Possible data: https://developer.vimeo.com/apis/simple#response-data
      });

    }
  };

  var vimeoCallbackUrl = function (id) {
    return 'https://vimeo.com/api/v2/video/' + id + '.json';
  };

  /*
   * Ensure that a handler is run before any other registered handlers,
   * independent of the order in which they were bound
   * As seen on https://stackoverflow.com/questions/2360655/jquery-event-handlers-always-execute-in-order-they-were-bound-any-way-around-t
   * and on https://gist.github.com/infostreams/6540654
   */
  $.fn.bindFirst = function (which, handler) {
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
      container: $('.container-lazyload'),
      selector: 'object, embed, iframe, .preview-lazyload, .lazy-load-div'
    },

    init: function () {
      if (responsiveVideos.config.container.length > 0) {
        var $window = $(window);
        $window.on('resize', responsiveVideos.resize);
        // Use bindFirst() to ensure that other plugins like Inline Comments work correctly (in case they depend on the video heights)
        $window.bindFirst('load', function () {
          responsiveVideos.resize();
        });
        $window.on('load', function () {
          responsiveVideos.resize();
          markInitialized();
        });
      }
    },

    resize: function () {
      $(responsiveVideos.config.selector, responsiveVideos.config.container).each(function () {

        var $this = $(this);
        var width = $this.parent().width();
        var height = Math.round(width * videoratio);

        $this.attr('height', height);
        $this.attr('width', width);
        $this.css({
          'height': height,
          'width': width,
        });

      });
    },

  };

  $(function () {
    lazyload_vimeo.init(lazyload_video_settings.vimeo);
  });

}(window.lazyload_vimeo = window.lazyload_vimeo || {}, jQuery));
