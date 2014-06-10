/*
 * Lazy Load Vimeo
 * by Kevin Weber
 */

var $llv = jQuery.noConflict();
$llv(document).ready(function() {

  var classPreviewVimeo = 'preview-vimeo';
    var classPreviewVimeoDot = '.'+classPreviewVimeo;


  function doload_llv() {
    vimeoCreateThumbProcess();

    // Replace thumbnail with iframe
    vimeoCreatePlayer();
  }

  var vimeoCreatePlayer = function() {
    $llv( classPreviewVimeoDot ).on('click', function()
      {
        var vid = getAttrId( this );
        $llv(this).html('<iframe src="//player.vimeo.com/video/' + vid + '?autoplay=1" style="height:'+(parseInt($llv("#"+vid).css("height")))+'px;width:100%" frameborder="0" webkitAllowFullScreen mozallowfullscreen autoPlay allowFullScreen></iframe>');
      }
    );
  };

  var vimeoCreateThumbProcess = function() {
    $llv( classPreviewVimeoDot ).each(function() {
      var vid = getAttrId( this );
      vimeoLoadingThumb( vid );
    });
  };

  var vimeoLoadingThumb = function( id ){
    var url = "//vimeo.com/api/v2/video/" + id + ".json?callback=showThumb";
      
    var script = document.createElement( 'script' );
    script.type = 'text/javascript';
    script.src = url;

    $llv("#" + id).prepend(script).prepend('<div style="height:'+(parseInt($llv("#" + id).css("height")))+'px;width:'+(parseInt($llv("#" + id).css("width")))+'px;" class="lazy-load-vimeo-div"><span class="titletext vimeo"></span></div>');
  };

  var getAttrId = function( element ) {
    var vid = $llv( element ).attr('id');
    return vid;
  };

  $llv(document).ready(doload_llv()).ajaxStop(function(){
    doload_llv();
  });
  
});