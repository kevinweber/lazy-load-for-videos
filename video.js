import { onBindFirstLoad } from '../utils/jQueryBindFirst';
import jQueryAjaxStop from '../utils/jQueryAjaxStop';
import findElements from '../utils/findElements';
import debounce from '../utils/debounce';

const videoratio = 0.5625;

export function setBackgroundImage(element, imageUrl) {
  // Don't simply set "background:url(...)..." because this prop would override
  // custom styling such as "background-size: cover".
  element.setAttribute('style', `background-image:url(${imageUrl});background-color:#000;background-position:center center;background-repeat:no-repeat;`);
}

const debouncedResize = debounce(() => {
  findElements('.container-lazyload').forEach((domContainerItem) => {
    /* change ratio */
    const embedelement = domContainerItem;
    let newvideoratio = 0.5625;
    if (embedelement.parentNode.parentNode.classList.contains('wp-has-aspect-ratio')) {
      const classes = String(domContainerItem.parentNode.parentNode.classList);
      const ratioclass = classes.substring(
        classes.lastIndexOf('wp-embed-aspect-'),
        classes.lastIndexOf(' '),
      );
      const ratioraw = ratioclass.replace('wp-embed-aspect-', '');
      const splitratio = ratioraw.split('-');
      newvideoratio = Number(splitratio[1]) / Number(splitratio[0]);
    }
    // console.log(newvideoratio);
    /* change ratio end */
    findElements('object, embed, iframe, .preview-lazyload, .lazy-load-div', domContainerItem)
      .forEach((domItem) => {
        const element = domItem;
        const width = element.parentNode.clientWidth;

        const height = Math.round(width * newvideoratio);

        element.setAttribute('height', `${height}px`);
        element.setAttribute('width', `${width}px`);
        element.style.height = `${height}px`;
        element.style.width = `${width}px`;
      });
  });
}, 100);

export function resizeResponsiveVideos() {
  debouncedResize();
}

function markInitialized(domSelector) {
  findElements(domSelector).forEach((domItem) => {
    domItem.parentNode.classList.remove('js-lazyload--not-loaded');
  });
}

function initResponsiveVideos(previewVideoSelector) {
  onBindFirstLoad(resizeResponsiveVideos);
  window.addEventListener('resize', resizeResponsiveVideos);
  window.addEventListener('load', () => {
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
