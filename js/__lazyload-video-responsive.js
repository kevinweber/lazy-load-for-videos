/*
 * Responsive Video
 * by Kevin Weber (kevinw.de)
 */

( function( $ ){

  // Helpers
  var videoratio = 0.5625;

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

    init: function( config ) {
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

  if (typeof responsiveVideos.init === 'function' ) { 
    responsiveVideos.init();
  }


})(jQuery);