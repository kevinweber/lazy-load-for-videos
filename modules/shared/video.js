import { onBindFirstLoad } from '../utils/jQueryBindFirst';
import jQueryAjaxStop from '../utils/jQueryAjaxStop';
import findElements from '../utils/findElements';
import debounce from '../utils/debounce';

export function setBackgroundImage(domNode, imageUrl) {
  const element = domNode;
  // Don't simply set "background:url(...)..." because this prop would override
  // custom styling such as "background-size: cover".
  element.style.backgroundImage = `url(${imageUrl})`;
  element.style.backgroundColor = '#000';
  element.style.backgroundPosition = 'center center';
  element.style.backgroundRepeat = 'no-repeat';
}

function determineVideoRatio(element) {
  const parent = element && element.parentNode && element.parentNode.parentNode;
  const hasAspectRatioClass = parent && parent.classList.contains('wp-has-aspect-ratio');
  const classes = String(parent.classList);
  const ratioclass = classes.substring(
    classes.lastIndexOf('wp-embed-aspect-'),
    classes.lastIndexOf(' '),
  ).trim();

  if (hasAspectRatioClass && ratioclass) {
    const ratioraw = ratioclass.replace('wp-embed-aspect-', '');
    const splitratio = ratioraw.split('-');
    const result = Number(splitratio[1]) / Number(splitratio[0]);
    const countDec = result.toString().split('.')[1].length;

    if (countDec > 4) {
      return Math.round(result * 10000) / 10000;
    }

    return result;
  }

  return 0.5625; // <-- default video ratio
}

export function resizeVideo(domContainerItem) {
  const videoRatio = determineVideoRatio(domContainerItem);
  findElements('object, embed, iframe, .preview-lazyload, .lazy-load-div', domContainerItem)
    .forEach((domItem) => {
      const element = domItem;
      const width = element.parentNode.clientWidth;
      const height = Math.round(width * videoRatio);

      element.setAttribute('height', `${height}px`);
      element.setAttribute('width', `${width}px`);
      element.style.height = `${height}px`;
      element.style.width = `${width}px`;
    });
}

const debouncedResize = debounce(() => {
  findElements('.container-lazyload').forEach(resizeVideo);
}, 100);

export function resizeResponsiveVideos() {
  debouncedResize();
}

function initResponsiveVideos() {
  onBindFirstLoad(resizeResponsiveVideos);
  window.addEventListener('resize', resizeResponsiveVideos);
  window.addEventListener('load', () => {
    resizeResponsiveVideos();
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
    resizeResponsiveVideos();
  });

  initResponsiveVideos(previewVideoSelector);

  if (typeof pluginOptions.callback === 'function') {
    pluginOptions.callback();
  }
}

export function inViewOnce(elements, onIntersect) {
  let observer;

  const options = {
    root: null,
    rootMargin: '100px',
  };

  function handleIntersectElement(element) {
    onIntersect(element);
    element.parentNode.classList.remove('js-lazyload--not-loaded');
    resizeVideo(element.parentNode);
  }

  if (!('IntersectionObserver' in window)
    && !('IntersectionObserverEntry' in window)
    && !('intersectionRatio' in window.IntersectionObserverEntry.prototype)) {
    // Fallback for browsers without IntersectionObserver
    elements.forEach(handleIntersectElement);
    return;
  }

  const handleIntersect = (entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        observer.unobserve(entry.target);
        handleIntersectElement(entry.target);
      }
    });
  };

  // Note: It would be better to have only one IntersectionObserver and then append all items into
  observer = new IntersectionObserver(handleIntersect, options);
  elements.forEach((element) => {
    observer.observe(element);
  });
}
