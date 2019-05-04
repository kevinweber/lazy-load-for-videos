import { init, resizeResponsiveVideos, setBackgroundImage } from '../shared/video';
import createElements from '../utils/createElements';
import findElements from '../utils/findElements';
import queryHashToString from '../utils/queryHashToString';

/*
 * Lazy Load Youtube
 * by Kevin Weber (www.kweber.com)
 */

// Classes
const classPreviewYoutube = 'preview-youtube';

// Helpers
let thumbnailurl = '';

let pluginOptions;
export const defaultPluginOptions = {
  colour: 'red', // possible: red, white
  controls: true,
  loadpolicy: true,
  modestbranding: false,
  relations: true,
  buttonstyle: '',
  preroll: '',
  postroll: '',
  responsive: true,
  thumbnailquality: '0',
  loadthumbnail: true,
  callback: null,
};

function removePlayerControls(element) {
  element.classList.remove(classPreviewYoutube);
}

export function getVideoUrl({
  pluginOptions: pluginOpts, videoId, startTime,
}) {
  const query = {
    autoplay: 1, // Always autoplay video!
  };

  if (pluginOpts.relations) query.rel = 0;
  if (pluginOpts.controls) query.controls = 0;
  if (pluginOpts.loadpolicy) query.iv_load_policy = 3;
  if (pluginOpts.modestbranding) query.modestbranding = 1;

  const preroll = pluginOpts.preroll !== videoId && pluginOpts.preroll;
  const postroll = pluginOpts.postroll !== videoId && pluginOpts.postroll;
  const playlistArray = [];
  if (preroll) playlistArray.push(preroll);
  if (postroll) playlistArray.push(postroll);
  if (playlistArray.length > 0) {
    query.playlist = playlistArray.join(',');
  }

  if (startTime && startTime !== 0) {
    query.start = startTime;
  }

  // TODO
  let colour = '';

  /*
   * Configure URL parameters
   */
  if (pluginOpts.colour !== undefined && pluginOpts.colour !== colour && pluginOpts.colour !== 'red') {
    colour = `&color=${pluginOpts.colour}`;
  }

  /*
   * Generate URL
   */
  // TODO: Verify all params: `${embedUrl}${(embedUrl.indexOf('?') === -1) ?
  // '?' : '&'}autoplay=1${colour}${controls}${loadpolicy}
  // ${modestbranding}${relations}${playlist}${embedstart}`;
  return `https://www.youtube.com/embed/${videoId}?${queryHashToString(query)}`;
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

function load() {
  findElements('a.lazy-load-youtube').forEach((domItem, index) => {
    const videoLinkElement = domItem;
    const href = videoLinkElement.getAttribute('href');
    const embedparms = getEmbedParams(href);

    /*
     * Load Youtube ID
     */
    const videoId = embedparms.split('?')[0].split('#')[0];

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

    let startTime = 0;
    const timeFactors = [3600, 60, 1]; // h, m, s
    let startMatch = embedparms.match(/[#&?]t=(?:(\d+)(?:h))?(?:(\d+)(?:m))?(?:(\d+)(?:s))?/);

    if (startMatch) {
      for (let s = 1; s < startMatch.length; s += 1) {
        if (typeof startMatch[s] !== 'undefined') {
          startTime += parseInt(startMatch[s], 10) * timeFactors[s - 1];
        }
      }
    }

    if (startTime === 0) {
      startMatch = embedparms.match(/[#&?](?:t|start)=(\d+)/);
      if (startMatch) {
        [, startTime] = startMatch;
      }
    }

    videoLinkElement.innerHTML = `<div aria-hidden="true" class="lazy-load-info"><span class="titletext youtube" itemprop="name">${videoTitle()}</span></div>`;

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
          setBackgroundImage(element, src);
        }

        img.parentNode.removeChild(img);
      });

      document.body.appendChild(img);
    }

    if (pluginOptions.loadthumbnail) {
      setBackgroundImg(videoLinkElement);
    }

    videoLinkElement.getAttribute('id', videoId + index);
    videoLinkElement.getAttribute('href', youtubeUrl(videoId) + (startTime ? `#t=${startTime}s` : ''));

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
      const videoUrl = getVideoUrl({
        pluginOptions, videoId, embedparms, startTime,
      });
      console.log(videoUrl);
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
    load, pluginOptions, previewVideoSelector: `.${classPreviewYoutube}`,
  });
}

export default lazyloadYoutube;
