import jQueryAjaxStop from '../shared-utils/jQueryAjaxStop';
import findElements from '../shared-utils/findElements';

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
  const aspectRatioClass = String(element?.parentNode?.parentNode?.classList).match(/wp-embed-aspect-\d+-\d+/);

  if (aspectRatioClass) {
    const ratioraw = aspectRatioClass[0].replace('wp-embed-aspect-', '');
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
  findElements(
    'object, embed, iframe, .preview-lazyload, .lazy-load-div',
    domContainerItem,
  ).forEach((domItem) => {
    const element = domItem;
    const width = element.parentNode.clientWidth;
    const height = Math.round(width * videoRatio);

    element.setAttribute('height', `${height}px`);
    element.setAttribute('width', `${width}px`);
    element.style.height = `${height}px`;
    element.style.width = `${width}px`;
  });
}

function resizeResponsiveVideos(rootNode) {
  requestAnimationFrame(() => {
    findElements('.container-lazyload', rootNode).forEach(resizeVideo);
  });
}

export function init({ load, pluginOptions }) {
  const { rootNode } = pluginOptions;
  const resizeFunc = () => resizeResponsiveVideos(rootNode);
  load(pluginOptions);

  /*
   * Use ajaxStop function to prevent plugin from breaking when another plugin uses Ajax
   */
  jQueryAjaxStop(() => {
    load(pluginOptions);
    resizeResponsiveVideos(rootNode);
  });

  window.addEventListener('resize', resizeFunc);
  window.addEventListener('load', resizeFunc);

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

  if (
    !('IntersectionObserver' in window) &&
    !('IntersectionObserverEntry' in window) &&
    !('intersectionRatio' in window.IntersectionObserverEntry.prototype)
  ) {
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
