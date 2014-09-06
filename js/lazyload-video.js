/*
 * Code that is used by video scripts
 * by Kevin Weber (kevinw.de)
 */

 var $lazyload_video = jQuery.noConflict();

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

      $( classBrandingDot ).css({
        'display': 'block',
        'visibility': 'visible',
      });

      // Get colour
      var color = $( classBrandingDot ).css('color');
      if ( color !== undefined ) {
        // Remove spaces
        color = color.replace(/\s/g, '');
        // Convert to lowercase
        color = color.toLowerCase();
        // When transparent: make it white
        if ( color === 'transparent' || color === 'rgba(0,0,0,0)' ) {
          $( classBrandingDot ).css("cssText", "color: white!important;");
        }
      }
    }
  };

}( window.lazyload_video = window.lazyload_video || {}, $lazyload_video ));