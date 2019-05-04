import { init, setBackgroundImage, resizeResponsiveVideos } from '../shared/video';
import createElements from '../utils/createElements';
import findElements from '../utils/findElements';

/*
 * Lazy Load Vimeo
 * by Kevin Weber (www.kweber.com)
 */

const $ = window.jQuery || window.$;

window.showThumb = (data) => {
  const relevantData = data[0];

  if (lazyload_video_settings.vimeo.loadthumbnail) {
    findElements(`[id="${relevantData.id}"]`).forEach((element) => {
      setBackgroundImage(element, relevantData.thumbnail_large);
    });
  }
};

// Classes
const classPreviewVimeo = 'preview-vimeo';
const classPreviewVimeoDot = `.${classPreviewVimeo}`;

let pluginOptions;
const defaultPluginOptions = {
  buttonstyle: '',
  playercolour: '',
  responsive: true,
  loadthumbnail: true,
  callback: null,
};

function removePlayerControls(element) {
  $(element).removeClass(classPreviewVimeo);
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

function vimeoLoadingThumb($container, id) {
  let script;

  if (lazyload_video_settings.vimeo.loadthumbnail) {
    script = document.createElement('script');
    script.type = 'text/javascript';
    script.src = `${vimeoCallbackUrl(id)}.json?callback=showThumb`;

    $container.after(script);
  }

  let info = '';
  if (lazyload_video_settings.vimeo.show_title) {
    const videoTitle = $container.attr('data-video-title');
    info = `<div aria-hidden="true" class="lazy-load-info"><span class="titletext vimeo" itemprop="name">${videoTitle}</span></div>`;
  }

  $container
    .prepend(info)
    .prepend('<div aria-hidden="true" class="lazy-load-div"></div>')
    .addClass(pluginOptions.buttonstyle);
}

function vimeoCreateThumbProcess() {
  $(classPreviewVimeoDot).each((index, item) => {
    const $item = $(item);
    const vid = $item.attr('id');

    // Remove no longer needed title (title is necessary for preview in text editor)
    $item.empty();

    vimeoLoadingThumb($item, vid);
  });
}

function vimeoCreatePlayer() {
  $(classPreviewVimeoDot).on('click', (event) => {
    event.preventDefault();
    const item = event.target;
    const vid = $(event.target).attr('id');

    removePlayerControls(item);

    let playercolour = '';
    if (pluginOptions.playercolour !== playercolour) {
      pluginOptions.playercolour = filterDotHash(pluginOptions.playercolour);
      playercolour = `&color=${pluginOptions.playercolour}`;
    }

    $(item).replaceWith(`<iframe src="${vimeoUrl(vid)}?autoplay=1${playercolour}" style="height:${parseInt($(`#${vid}`).css('height'), 10)}px;width:100%" frameborder="0" webkitAllowFullScreen mozallowfullscreen autoPlay allowFullScreen></iframe>`);
    if (pluginOptions.responsive === true) {
      resizeResponsiveVideos();
    }
  });
}

function load() {
  vimeoCreateThumbProcess();

  // Replace thumbnail with iframe
  vimeoCreatePlayer();
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
