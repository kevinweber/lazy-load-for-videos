import jQueryAjaxStop from '../utils/jQueryAjaxStop';
import findElements from '../utils/findElements';

const $ = window.jQuery || window.$;
const videoratio = 0.5625;

/*
 * The following code bases on "Responsive Video Embeds" by Kevin Leary
 */
const responsiveVideosConfig = {
  container: '.container-lazyload',
  selector: 'object, embed, iframe, .preview-lazyload, .lazy-load-div',
};

export function setBackgroundImage(element, imageUrl) {
  // Don't simply set "background:url(...)..." because this prop would override
  // custom styling such as "background-size: cover".
  element.setAttribute('style', `background-image:url(${imageUrl});background-color:#000;background-position:center center;background-repeat:no-repeat;`);
}

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

function markInitialized(domSelector) {
  findElements(domSelector).forEach((item) => {
    item.parentNode.classList.remove('js-lazyload--not-loaded');
  });
}

function initResponsiveVideos(previewVideoSelector) {
  const $window = $(window);
  $window.on('resize', resizeResponsiveVideos);
  // Use bindFirst() to ensure that other plugins like Inline Comments
  // work correctly (in case they depend on the video heights)
  $window.bindFirst('load', () => {
    resizeResponsiveVideos();
  });
  $window.on('load', () => {
    resizeResponsiveVideos();
    markInitialized(previewVideoSelector);
  });
}

export function init({
  load, pluginOptions, previewVideoSelector,
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
    markInitialized(previewVideoSelector);
  });

  if (pluginOptions.responsive === true) {
    initResponsiveVideos(previewVideoSelector);
  } else {
    markInitialized(previewVideoSelector);
  }

  if (typeof pluginOptions.callback === 'function') {
    pluginOptions.callback();
  }
}
