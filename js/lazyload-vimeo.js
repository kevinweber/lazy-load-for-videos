/*
 * Lazy Load Vimeo
 * by Kevin Weber (kevinw.de)
 */

var $llv = jQuery.noConflict();
var $llv_o;
var setOptionsVimeo = function(options) {
  $llv_o = $llv.extend({
      playercolour: '',
    },
    options);
};

$llv(document).ready(function() {

  var classPreviewVimeo = 'preview-vimeo';
  var classPreviewVimeoDot = '.' + classPreviewVimeo;


  function doload_llv() {
    vimeoCreateThumbProcess();

    // Replace thumbnail with iframe
    vimeoCreatePlayer();
  }

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

  $llv(document).ready(doload_llv()).ajaxStop(function() {
    doload_llv();
  });

});