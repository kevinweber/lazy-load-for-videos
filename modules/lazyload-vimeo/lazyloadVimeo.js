import { init, resizeResponsiveVideos, setBackgroundImage } from '../shared/video';
import createElements from '../utils/createElements';
import findElements from '../utils/findElements';

/*
 * Lazy Load Vimeo
 * by Kevin Weber (www.kweber.com)
 */

window.showThumb = (data) => {
  const relevantData = data[0];

  if (lazyload_video_settings.vimeo.loadthumbnail) {
    findElements(`[id="${relevantData.id}"]`).forEach((domItem) => {
      setBackgroundImage(domItem, relevantData.thumbnail_large);
    });
  }
};

// Classes
const classPreviewVimeo = 'preview-vimeo';

let pluginOptions;
const defaultPluginOptions = {
  buttonstyle: '',
  playercolour: '',
  responsive: true,
  loadthumbnail: true,
  // callback: null, // <- Currently not supported
};

function removePlayerControls(element) {
  element.classList.remove(classPreviewVimeo);
}

function vimeoUrl(videoId) {
  return `https://player.vimeo.com/video/${videoId}`;
}

// Remove dots and hashs from a string
function filterDotHash(variable) {
  const filterdothash = variable.toString().replace(/[.#]/g, '');
  return filterdothash;
}

function vimeoCallbackUrl(thumbnailId) {
  return `https://vimeo.com/api/v2/video/${thumbnailId}.json`;
}

function vimeoLoadingThumb(videoLinkElement, id) {
  if (lazyload_video_settings.vimeo.loadthumbnail) {
    const script = document.createElement('script');
    script.src = `${vimeoCallbackUrl(id)}.json?callback=showThumb`;
    videoLinkElement.parentNode.insertBefore(script, videoLinkElement.firstChild);
  }

  let info = '';
  if (lazyload_video_settings.vimeo.show_title) {
    const videoTitle = videoLinkElement.getAttribute('data-video-title');
    info = `<div aria-hidden="true" class="lazy-load-info"><span class="titletext vimeo" itemprop="name">${videoTitle}</span></div>`;
  }

  const lazyloadDiv = createElements(`${info}<div aria-hidden="true" class="lazy-load-div"></div>`);
  videoLinkElement.insertBefore(lazyloadDiv, videoLinkElement.firstChild);
  videoLinkElement.classList.add(pluginOptions.buttonstyle);
}

function vimeoCreateThumbProcess(videoLinkElement) {
  const previewItem = videoLinkElement;
  const vid = previewItem.getAttribute('id');

  // Remove no longer needed title (title is necessary for preview in text editor)
  previewItem.innerHTML = '';

  vimeoLoadingThumb(previewItem, vid);
}

function vimeoCreatePlayer(videoLinkElement) {
  videoLinkElement.addEventListener('click', (event) => {
    event.preventDefault();
    const item = event.target;
    const vid = item.getAttribute('id');

    removePlayerControls(item);

    let playercolour = '';
    if (pluginOptions.playercolour !== playercolour) {
      pluginOptions.playercolour = filterDotHash(pluginOptions.playercolour);
      playercolour = `&color=${pluginOptions.playercolour}`;
    }

    const videoIFrame = createElements(`<iframe src="${vimeoUrl(vid)}?autoplay=1${playercolour}" style="height:${parseInt(item.clientHeight, 10)}px;width:100%" frameborder="0" webkitAllowFullScreen mozallowfullscreen autoPlay allowFullScreen></iframe>`);
    item.parentNode.replaceChild(videoIFrame, item);

    if (pluginOptions.responsive === true) {
      resizeResponsiveVideos();
    }
  });
}

function load() {
  findElements(`.${classPreviewVimeo}`).forEach((domItem) => {
    vimeoCreateThumbProcess(domItem);
    // Replace thumbnail with iframe
    vimeoCreatePlayer(domItem);
  });
}

const lazyloadVimeo = (options) => {
  pluginOptions = {
    ...defaultPluginOptions,
    ...options,
  };

  init({
    load, pluginOptions, previewVideoSelector: `.${classPreviewVimeo}`,
  });
};

export default lazyloadVimeo;
