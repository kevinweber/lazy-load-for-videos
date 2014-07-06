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


var $llv_o;
var setOptionsVimeo = function(options) {
  $llv_o = $llv.extend({
      playercolour: '',
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
        return '<a class="' + classBranding + '" href="http://kevinw.de/lazyloadvideos" title="Lazy Load for Videos by Kevin Weber" target="_blank">i</a>';
      };

      /*
       * Create info element
       */
      var createPluginInfo = function() {
        // source = Video
        var source = $llv( classPreviewVimeoDot );
        // element = Plugin info element
        var element = $llv( loadPluginInfo() );
        // Prepend element to source
        source.before( element );
      };


  var vimeoCreatePlayer = function() {
    $llv(classPreviewVimeoDot).on('click', function() {
      var vid = getAttrId(this);

      var playercolour = '';
      if ($llv_o.playercolour !== playercolour) {
        $llv_o.playercolour = filterDotHash($llv_o.playercolour);
        playercolour = '&color=' + $llv_o.playercolour;
      }

      $llv(this).html('<iframe src="//player.vimeo.com/video/' + vid + '?autoplay=1' + playercolour + '" style="height:' + (parseInt($llv("#" + vid).css("height"))) + 'px;width:100%" frameborder="0" webkitAllowFullScreen mozallowfullscreen autoPlay allowFullScreen></iframe>');
    });
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
    var url = "//vimeo.com/api/v2/video/" + id + ".json?callback=showThumb";

    var script = document.createElement('script');
    script.type = 'text/javascript';
    script.src = url;

    $llv("#" + id).prepend(script).prepend('<div style="height:' + (parseInt($llv("#" + id).css("height"))) + 'px;width:' + (parseInt($llv("#" + id).css("width"))) + 'px;" class="lazy-load-vimeo-div"><span class="titletext vimeo"></span></div>');
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
   * Prevent users from removing branding // YOU'RE NOT ALLOWED TO EDIT THE FOLLOWING LINES OF CODE
   */
  var displayBranding = function() {
    if ($llv_o.displayBranding !== false) {
      $llv(classBrandingDot).css({
        'display': 'block',
        'visibility': 'visible',
      });
    }
  };
  displayBranding();

});