import jQueryAjaxStop from '../utils/jQueryAjaxStop';

const $ = window.jQuery || window.$;

export const videoratio = 0.5625;

/*
 * The following code bases on "Responsive Video Embeds" by Kevin Leary
 */
const responsiveVideosConfig = {
  container: '.container-lazyload',
  selector: 'object, embed, iframe, .preview-lazyload, .lazy-load-div',
};

export function resizeResponsiveVideos() {
  $(responsiveVideosConfig.container).find(responsiveVideosConfig.selector)
    .each((index, item) => {
      const $item = $(item);
      const width = $item.parent().width();
      const height = Math.round(width * videoratio);

      $item.attr('height', height);
      $item.attr('width', width);
      $item.css({
        height,
        width,
      });
    });
}

function initResponsiveVideos(markInitialized) {
  const $window = $(window);
  $window.on('resize', resizeResponsiveVideos);
  // Use bindFirst() to ensure that other plugins like Inline Comments
  // work correctly (in case they depend on the video heights)
  $window.bindFirst('load', () => {
    resizeResponsiveVideos();
  });
  $window.on('load', () => {
    resizeResponsiveVideos();
    markInitialized();
  });
}

export function init({
  load, pluginOptions, markInitialized,
}) {
  load();

  /*
   * Use ajaxStop function to prevent plugin from breaking when another plugin uses Ajax
   */
  jQueryAjaxStop(() => {
    load();
    if (pluginOptions.responsive === true) {
      resizeResponsiveVideos();
    }
    markInitialized();
  });

  if (pluginOptions.responsive === true) {
    initResponsiveVideos(markInitialized);
  } else {
    markInitialized();
  }

  if (typeof pluginOptions.callback === 'function') {
    pluginOptions.callback();
  }
}
