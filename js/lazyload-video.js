/*
 * Code that is used by video scripts
 * by Kevin Weber (kevinw.de)
 */
( function( lazyload_video, $, undefined ){

  // Classes
  var classBranding = 'lazyload-info-icon';
    var classBrandingDot = '.' + classBranding;

  lazyload_video.init = function( options ) {
    setOptions( options );
    displayBranding();
  };

  var $_o;
  var setOptions = function( options ) {
    $_o = $.extend({
        displayBranding: true,
      },
      options);
  };

  /*
   * Prevent users from removing branding // YOU'RE NOT ALLOWED TO EDIT THE FOLLOWING LINES OF CODE
   */
  var displayBranding = function() {

    // DON'T BE EVIL - IS THIS ACTUALLY WORTH THE EFFORT?

    if ($_o.displayBranding !== false) {

      var $element = $( classBrandingDot );

      $element.attr("style", "display:block!important;visibility:visible!important");

      // When the opacity/alpha is to low, increase opacity and color it black
      if ( 
          ( $element.css( "opacity" ) < 0.2 ) ||
          ( getAlpha( $element ) < 0.2 )
        )
      {
        $element.css({'color':'rgba(0,0,0,1)'}).fadeTo( "fast", 0.5 );
      }

      // When the font size is to low, increase it
      var $fontsize = $element.css( "font-size" );
      if ( $fontsize !== undefined ) {
        // Remove everything but numbers
        $fontsize = $fontsize.replace(/\D/g,'');

        // Increase size when to low
        if ( $fontsize < 6 ) {
          $element.css({'font-size':'14px'});
        }
      }

      // Get colour
      var color = $element.css('color');
      if ( color !== undefined ) {
        // Test if spaces or tab stops exist
        if ( /\s/g.test(color) ) {
          // Remove spaces
          color = color.replace(/\s/g, '');
        }
        // Convert to lowercase
        color = color.toLowerCase();
      }
      // When transparent: make it white
      if ( color === 'transparent' || color === 'transparent!important' || color === 'rgba(0,0,0,0)' || color === 'rgba(255,255,255,0)' ) {
        $element.css("cssText", "color: white!important;");
      }

    }
  };

  /*
   * Test if element's color contains a RGBA value.
   * If yes,  @return integer
   *          else @return 1
   */
  var getAlpha = function( element ) {
    var alpha = 1;
    var color = element.css( 'color' );

    // Search color value for string "rgba" (case-insensitive)
    if ( /rgba/i.test( color ) ) {
      // Get the fourth (alpha) value using string replace
      alpha = color.replace(/^.*,(.+)\)/,'$1');
    }

    return alpha;
  };

  $(function() {
    lazyload_video.init(lazyload_video_settings.video);
  });

}( window.lazyload_video = window.lazyload_video || {}, jQuery ));