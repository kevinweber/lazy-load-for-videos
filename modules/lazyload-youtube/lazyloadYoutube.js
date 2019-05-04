import { init, resizeResponsiveVideos } from '../shared/video';
import createElements from '../utils/createElements';
import findElements from '../utils/findElements';
import getJson from '../utils/getJson';

/*
 * Lazy Load Youtube
 * by Kevin Weber (www.kweber.com)
 */

// Classes
const classPreviewYoutube = 'preview-youtube';

// Helpers
let thumbnailurl = '';

let pluginOptions;
const defaultPluginOptions = {
  theme: 'dark', // possible: dark, light
  colour: 'red', // possible: red, white
  controls: true,
  loadpolicy: true,
  modestbranding: false,
  showinfo: true,
  relations: true,
  buttonstyle: '',
  preroll: '',
  postroll: '',
  videoseo: false,
  responsive: true,
  thumbnailquality: '0',
  loadthumbnail: true,
  callback: null,
};

function markInitialized() {
  findElements(`.${classPreviewYoutube}`).forEach((item) => {
    item.parentNode.classList.remove('js-lazyload--not-loaded');
  });
}

function removePlayerControls(element) {
  element.classList.remove(classPreviewYoutube);
}

function getVideoUrl(preroll, videoId, emu, embedstart) {
  let theme = '';
  let colour = '';
  let postroll = '';
  let playlist = '';
  let overridePreroll = preroll;

  /*
   * Configure URL parameters
   */
  if (pluginOptions.theme !== undefined && pluginOptions.theme !== theme && pluginOptions.theme !== 'dark') {
    theme = `&theme=${pluginOptions.theme}`;
  }
  if (pluginOptions.colour !== undefined && pluginOptions.colour !== colour && pluginOptions.colour !== 'red') {
    colour = `&color=${pluginOptions.colour}`;
  }

  const showinfo = pluginOptions.showinfo ? '' : '&showinfo=0';
  const relations = pluginOptions.relations ? '' : '&rel=0';
  const controls = pluginOptions.controls ? '' : '&controls=0';
  const loadpolicy = pluginOptions.loadpolicy ? '' : '&iv_load_policy=3';
  const modestbranding = pluginOptions.modestbranding ? '&modestbranding=1' : '';

  /*
   * Configure URL parameter 'playlist'
   */
  if (preroll !== videoId) {
    overridePreroll = `${videoId},`;
  } else {
    overridePreroll = '';
  }
  if ((pluginOptions.postroll !== undefined) && (pluginOptions.postroll !== postroll)) {
    ({ postroll } = pluginOptions);
  }
  if ((preroll !== '') || (postroll !== '')) {
    playlist = `&playlist=${overridePreroll}${postroll}`;
  }

  /*
   * Generate URL
   */
  return `${emu}${(emu.indexOf('?') === -1) ? '?' : '&'}autoplay=1${theme}${colour}${controls}${loadpolicy}${modestbranding}${showinfo}${relations}${playlist}${embedstart}`;
}

/*
 * Load parameters from user's original Youtube URL
 */
function getEmbedParams(href) {
  let params = '';

  [, params] = href.split('/embed/');
  if (!params) {
    [, params] = href.split('://youtu.be/');
  }
  if (!params) {
    params = href.split('v=')[1].replace(/&/, '?');
  }

  return params;
}

function getVideoIdPreroll(preroll, defaultParams) {
  if (pluginOptions.preroll !== undefined && pluginOptions.preroll !== preroll) {
    return pluginOptions.preroll;
  }

  // Fallback when no preroll ID should be loaded
  return defaultParams;
}

function load() {
  findElements('a.lazy-load-youtube').forEach((item, index) => {
    const videoLinkElement = item;
    const href = videoLinkElement.getAttribute('href');
    let embedparms = getEmbedParams(href);
    const preroll = '';

    /*
     * Load Youtube ID
     */
    const videoId = embedparms.split('?')[0].split('#')[0];

    const emu = `https://www.youtube.com/embed/${getVideoIdPreroll(preroll, embedparms)}`;

    function videoTitle() {
      if (videoLinkElement.getAttribute('data-video-title') !== undefined) {
        return videoLinkElement.getAttribute('data-video-title');
      } if (videoLinkElement.innerHTML) {
        return videoLinkElement.innerHTML;
      }
      return '';
    }

    function youtubeUrl(id) {
      return `https://www.youtube.com/watch?v=${id}`;
    }

    let start = 0;
    const timeFactors = [3600, 60, 1]; // h, m, s
    let startMatch = embedparms.match(/[#&?]t=(?:(\d+)(?:h))?(?:(\d+)(?:m))?(?:(\d+)(?:s))?/);

    if (startMatch) {
      for (let s = 1; s < startMatch.length; s += 1) {
        if (typeof startMatch[s] !== 'undefined') {
          start += parseInt(startMatch[s], 10) * timeFactors[s - 1];
        }
      }
    }

    if (start === 0) {
      startMatch = embedparms.match(/[#&?](?:t|start)=(\d+)/);
      if (startMatch) {
        [, start] = startMatch;
      }
    }

    [embedparms] = embedparms.split('#');
    let embedstart = '';
    if (start && start !== 0 && embedparms.indexOf('start=') === -1) {
      embedstart = `${(embedparms.indexOf('?') === -1) ? '?' : '&'}start=${start}`;
    }

    let itempropName = '';
    if (pluginOptions.videoseo === true) {
      itempropName = ' itemprop="name"';
    }

    if (embedparms.indexOf('showinfo=0') !== -1) {
      videoLinkElement.innerHTML = '';
    } else {
      videoLinkElement.innerHTML = `<div aria-hidden="true" class="lazy-load-info"><span class="titletext youtube"${itempropName}>${videoTitle()}</span></div>`;
    }

    const lazyloadDiv = createElements('<div aria-hidden="true" class="lazy-load-div"></div>');
    videoLinkElement.insertBefore(lazyloadDiv, videoLinkElement.firstChild);
    videoLinkElement.classList.add(pluginOptions.buttonstyle);

    /*
     * Set thumbnail URL
     */
    function setThumbnailUrl(thumbnailId) {
      const $url = `https://i2.ytimg.com/vi/${thumbnailId}/${pluginOptions.thumbnailquality}.jpg`;

      thumbnailurl = $url;
    }
    setThumbnailUrl(videoId);

    /*
     * Get thumbnail URL
     */
    function getThumbnailUrl() {
      return thumbnailurl;
    }

    function setBackgroundImg(element) {
      let src = getThumbnailUrl();

      // Create a temporary image. Once it is loaded, we can update the video element
      // using the src of this temporary image, then remove this temporary image.
      const img = createElements(`<img style="display:none" src="${src}">`).firstChild;

      img.addEventListener('load', () => {
        // If the max resolution thumbnail is not available, fall back to smaller size.
        // But note that we'll still see an 404 error in the console in this case.
        if (img.clientWidth === 120) {
          src = src.replace('maxresdefault', '0');
        }

        if (!element.style.backgroundImage) {
          // Don't simply set "background:url(...)..." because this prop would override
          // custom styling such as "background-size: cover".
          element.setAttribute('style', `background-image:url(${src});background-color:#000;background-position:center center;background-repeat:no-repeat;`);
        }

        img.parentNode.removeChild(img);
      });

      document.body.appendChild(img);
    }

    if (pluginOptions.loadthumbnail) {
      setBackgroundImg(videoLinkElement);
    }

    if (pluginOptions.videoseo === true) {
      const metaElements = createElements(`
        <meta itemprop="contentLocation" content="${youtubeUrl(videoId)}" />
        <meta itemprop="embedUrl" content="${emu}" />
        <meta itemprop="thumbnail" content="${getThumbnailUrl()}" />
      `);
      videoLinkElement.appendChild(metaElements);

      getJson(`https://gdata.youtube.com/feeds/api/videos/${videoId}?v=2&alt=jsonc&callback=?`, (data) => {
        const moreMetaElements = createElements(`
        <meta itemprop="datePublished" content="${data.data.uploaded}" />
        <meta itemprop="duration" content="${data.data.duration}" />
        <meta itemprop="aggregateRating" content="${data.data.rating}" />
      `);
        videoLinkElement.appendChild(moreMetaElements);
        // TODO: Retrieve and use even more data for Video SEO.
        // Get possible response data with //www.jsoneditoronline.org/ and
        // gdata.youtube.com/feeds/api/videos/pk99sSGF0YE?v=2&alt=jsonc
      });
    }

    videoLinkElement.getAttribute('id', videoId + index);
    videoLinkElement.getAttribute('href', youtubeUrl(videoId) + (start ? `#t=${start}s` : ''));

    /*
     * Register "onclick" event handler
     */
    videoLinkElement.addEventListener('click', (event) => {
      event.preventDefault();

      const eventTarget = event.target;
      removePlayerControls(eventTarget);

      /*
       * Generate iFrame
       */
      const videoUrl = getVideoUrl(preroll, videoId, emu, embedstart);
      const videoIFrame = createElements(`<iframe width="${parseInt(videoLinkElement.clientWidth, 10)}" height="${parseInt(videoLinkElement.clientHeight, 10)}" style="vertical-align:top;" src="${videoUrl}" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>`);

      eventTarget.parentNode.replaceChild(videoIFrame, eventTarget);

      if (pluginOptions.responsive === true) {
        resizeResponsiveVideos();
      }
    });
  });
}

function lazyloadYoutube(options) {
  pluginOptions = {
    ...defaultPluginOptions,
    ...options,
  };

  init({
    load, pluginOptions, markInitialized,
  });
}

export default lazyloadYoutube;
