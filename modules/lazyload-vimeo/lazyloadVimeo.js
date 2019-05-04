import { init, resizeResponsiveVideos } from '../shared/video';

/*
 * Lazy Load Vimeo
 * by Kevin Weber (www.kweber.com)
 */

const $ = window.jQuery || window.$;

window.showThumb = (data) => {
  const relevantData = data[0];

  if (lazyload_video_settings.vimeo.loadthumbnail) {
    $(`[id=${relevantData.id}]`)
      .css('background-image', `url(${relevantData.thumbnail_large})`)
      .css('background-color', '#000')
      .css('background-position', 'center center')
      .css('background-repeat', 'no-repeat');
  }
};

// Classes
const classPreviewVimeo = 'preview-vimeo';
const classPreviewVimeoDot = `.${classPreviewVimeo}`;
const classNotLoaded = 'js-lazyload--not-loaded';

// Helpers
function markInitialized() {
  $(classPreviewVimeoDot).parent().removeClass(classNotLoaded);
}

let pluginOptions;
const defaultPluginOptions = {
  buttonstyle: '',
  playercolour: '',
  videoseo: false,
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

function vimeoVideoSeo(id) {
  if (pluginOptions.videoseo === true) {
    $.getJSON(`${vimeoCallbackUrl(id)}?callback=?`, {
      format: 'json',
    }, (data) => {
      const relevantData = data[0];

      $(`#${id}`)
        .append(`<meta itemprop="contentLocation" content="${relevantData.url}" />`)
        .append(`<meta itemprop="embedUrl" content="${vimeoUrl(id)}" />`)
        .append(`<meta itemprop="thumbnail" content="${relevantData.thumbnail_large}" />`)
        .append(`<meta itemprop="datePublished" content="${relevantData.upload_date}" />`)
        .append(`<meta itemprop="duration" content="${relevantData.duration}" />`)
        .append(`<meta itemprop="aggregateRating" content="${data.data.rating}" />`);
      // TODO: Retrieve and use even more data for Video SEO. Possible data: https://developer.vimeo.com/apis/simple#response-data
    });
  }
}

function vimeoLoadingThumb($container, id) {
  let script;

  if (lazyload_video_settings.vimeo.loadthumbnail) {
    script = document.createElement('script');
    script.type = 'text/javascript';
    script.src = `${vimeoCallbackUrl(id)}.json?callback=showThumb`;

    $container.after(script);
  }

  let itempropName = '';
  if (pluginOptions.videoseo === true) {
    itempropName = ' itemprop="name"';
  }

  let info = '';
  if (lazyload_video_settings.vimeo.show_title) {
    const videoTitle = $container.attr('data-video-title');
    info = `<div aria-hidden="true" class="lazy-load-info"><span class="titletext vimeo"${itempropName} >${videoTitle}</span></div>`;
  }

  $container
    .prepend(info)
    .prepend(`<div aria-hidden="true" style="height:${parseInt($(`#${id}`).css('height'), 10)}px;width:${parseInt($(`#${id}`).css('width'), 10)}px;" class="lazy-load-div"></div>`)
    .addClass(pluginOptions.buttonstyle);

  vimeoVideoSeo(id);
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
    load, pluginOptions, markInitialized,
  });
};

export default lazyloadVimeo;
