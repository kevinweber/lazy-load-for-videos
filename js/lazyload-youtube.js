/*
 * Lazy Load Youtube
 * by Kevin Weber
 */

var $lly = jQuery.noConflict();
var $lly_o;
var setOptionsYoutube = function( options ) {
  $lly_o = $lly.extend( {
      theme: 'dark',  // possible: dark, light
      colour: 'red',  // possible: red, white
      controls: true,
      relations: true,
      playlist: '',
    },
  options);
};

$lly(document).ready(function() {

  var doload_lly = function() {

    $lly("a.lazy-load-youtube").each(function(index) {
      var embedparms = $lly(this).attr("href").split("/embed/")[1];
      if(!embedparms) {embedparms = $lly(this).attr("href").split("://youtu.be/")[1];}
      if(!embedparms) {embedparms = $lly(this).attr("href").split("v=")[1].replace(/\&/,'?');}
      var youid = embedparms.split("?")[0].split("#")[0];
      var start = embedparms.match(/[#&]t=(\d+)s/);
      if(start) {start = start[1];}
      else {
        start = embedparms.match(/[#&]t=(\d+)m(\d+)s/);
        if(start) {start = parseInt(start[1])*60+parseInt(start[2]);}
        else {
          start = embedparms.match(/[?&]start=(\d+)/);
          if(start) {start = start[1];}
        }
      }
      embedparms = embedparms.split("#")[0];
      if(start && embedparms.indexOf("start=") === -1)
        {embedparms += ((embedparms.indexOf("?")===-1) ? "?" : "&") + "start="+start;}
      if(embedparms.indexOf("showinfo=0") !== -1)
        {$lly(this).html('');}
      else
        {$lly(this).html('<div class="lazy-load-youtube-info"><span class="titletext youtube">' + $lly(this).attr("video-title") + '</div></div>');}
      $lly(this).prepend('<div style="height:'+(parseInt($lly(this).css("height"))-4)+'px;width:'+(parseInt($lly(this).css("width"))-4)+'px;" class="lazy-load-youtube-div"></div>');
      $lly(this).css("background", "#000 url(//i2.ytimg.com/vi/"+youid+"/0.jpg) center center no-repeat");
      $lly(this).attr("id", youid+index);
      $lly(this).attr("href", "//www.youtube.com/watch?v="+youid+(start ? "#t="+start+"s" : ""));
      var emu = '//www.youtube.com/embed/'+embedparms;
      var relations = '';
      if(!$lly_o.relations) {
        relations = '&rel=0';
      }
      var controls = '';
      if(!$lly_o.controls) {
        controls = '&controls=0';
      }
      var playlist = '';
      if( $lly_o.playlist !== playlist && $lly_o.playlist !== undefined ) {
        playlist = '&playlist='+$lly_o.playlist;
      }
      emu += ((emu.indexOf("?")===-1) ? "?" : "&") + "autoplay=1" + "&theme="+$lly_o.theme + "&color="+$lly_o.colour + controls + relations + playlist;
      var videoFrame = '<iframe width="'+parseInt($lly(this).css("width"))+'" height="'+parseInt($lly(this).css("height"))+'" style="vertical-align:top;" src="'+emu+'" frameborder="0" allowfullscreen></iframe>';
      $lly(this).attr("onclick", "$lly('#"+youid+index+"').replaceWith('"+videoFrame+"');return false;");
    });

  };

  $lly(document).ready(doload_lly()).ajaxStop(function(){
    doload_lly();
  });

});