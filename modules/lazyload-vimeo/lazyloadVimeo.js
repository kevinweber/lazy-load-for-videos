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
const classBranding = 'lazyload-info-icon';
const classBrandingDot = `.${classBranding}`;
const classNotLoaded = 'js-lazyload--not-loaded';

// Helpers
const videoratio = 0.5625;

function markInitialized() {
  $(classPreviewVimeoDot).parent().removeClass(classNotLoaded);
}

let $Options;
function mergeOptions(options) {
  $Options = $.extend({
    buttonstyle: '',
    playercolour: '',
    videoseo: false,
    responsive: true,
    displayBranding: false,
    loadthumbnail: true,
    callback: null,
  },
  options);
}

/*
 * Load plugin info
 */
function loadPluginInfo() {
  return `<a class="${classBranding}" href="https://www.kweber.com/lazy-load-videos/" title="Lazy Load for Videos by Kevin Weber" target="_blank">i</a>`;
}

/*
 * Create info element
 */
function createPluginInfo() {
  if (
    ($Options.displayBranding === true)
    // This prevents the site from creating unnecessary duplicate brandings
    && ($(classPreviewVimeoDot).siblings(classBrandingDot).length === 0)
  ) {
    // source = Video
    const source = $(classPreviewVimeoDot);
    // element = Plugin info element
    const element = $(loadPluginInfo());
    // Prepend element to source
    source.before(element);
  }
}

function removePlayerControls(element) {
  $(element).removeClass(classPreviewVimeo);
}
function removeBranding(element) {
  $(element).prev(classBrandingDot).remove();
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
  if ($Options.videoseo === true) {
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
  if ($Options.videoseo === true) {
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
    .addClass($Options.buttonstyle);

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


/*
 * The following code bases on "Responsive Video Embeds" by Kevin Leary
 */
const responsiveVideos = {
  config: {
    container: '.container-lazyload',
    selector: 'object, embed, iframe, .preview-lazyload, .lazy-load-div',
  },

  init() {
    if (responsiveVideos.config.container.length > 0) {
      const $window = $(window);
      $window.on('resize', responsiveVideos.resize);
      // Use bindFirst() to ensure that other plugins like Inline Comments
      // work correctly (in case they depend on the video heights)
      $window.bindFirst('load', () => {
        responsiveVideos.resize();
      });
      $window.on('load', () => {
        responsiveVideos.resize();
        markInitialized();
      });
    }
  },

  resize() {
    $(responsiveVideos.config.container).find(responsiveVideos.config.selector)
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
  },
};

function vimeoCreatePlayer() {
  $(classPreviewVimeoDot).on('click', (event) => {
    event.preventDefault();
    const item = event.target;
    const vid = $(event.target).attr('id');

    removePlayerControls(item);
    removeBranding(item);

    let playercolour = '';
    if ($Options.playercolour !== playercolour) {
      $Options.playercolour = filterDotHash($Options.playercolour);
      playercolour = `&color=${$Options.playercolour}`;
    }

    $(item).replaceWith(`<iframe src="${vimeoUrl(vid)}?autoplay=1${playercolour}" style="height:${parseInt($(`#${vid}`).css('height'), 10)}px;width:100%" frameborder="0" webkitAllowFullScreen mozallowfullscreen autoPlay allowFullScreen></iframe>`);
    if (typeof responsiveVideos.resize === 'function' && $Options.responsive === true) {
      responsiveVideos.resize();
    }
  });
}

function load() {
  vimeoCreateThumbProcess();
  createPluginInfo();

  // Replace thumbnail with iframe
  vimeoCreatePlayer();
}

const init = (options) => {
  mergeOptions(options);

  /*
   * Use ajaxStop function to prevent plugin from breaking when another plugin uses Ajax
   */
  $(document).ready(load()).ajaxStop(() => {
    load();
    if (typeof responsiveVideos.resize === 'function' && $Options.responsive === true) {
      responsiveVideos.resize();
    }
    markInitialized();
  });

  if (typeof responsiveVideos.init === 'function' && $Options.responsive === true) {
    responsiveVideos.init();
  } else {
    markInitialized();
  }

  if (typeof $Options.callback === 'function') {
    $Options.callback();
  }
};

export default init;
