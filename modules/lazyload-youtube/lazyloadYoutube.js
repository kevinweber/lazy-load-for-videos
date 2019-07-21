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
  colour: 'red', // supported colours: red, white
  controls: true,
  loadpolicy: true,
  buttonstyle: '',
  preroll: '',
  postroll: '',
  responsive: true,
  thumbnailquality: '0',
  loadthumbnail: true,
  // callback: null, // <- Currently not supported
};

function removePlayerControls(element) {
  element.classList.remove(classPreviewYoutube);
}

export function convertToSeconds(timestring) {
  let startTime = 0;
  const timeFactors = [3600, 60, 1]; // h, m, s
  const startMatch = timestring.match(/(?:(\d+)(?:h))?(?:(\d+)(?:m))?(?:(\d+)(?:s))?/);

  if (startMatch) {
    for (let s = 1; s < startMatch.length; s += 1) {
      if (typeof startMatch[s] !== 'undefined') {
        startTime += parseInt(startMatch[s], 10) * timeFactors[s - 1];
      }
    }
  }

  return startTime;
}

export function getVideoUrl({
  pluginOptions: pluginOpts, videoId, urlOptions,
}) {
  // First video changes if the preroll feature is used
  let firstVideoToPlay = videoId;
  const query = {
    autoplay: 1, // Always autoplay video once we load the iframe
    modestbranding: 1,
    rel: 0, // "0" means: Show related videos from the same channel
  };

  if (pluginOpts.controls === false) query.controls = 0;
  if (pluginOpts.loadpolicy) query.iv_load_policy = 3;
  if (pluginOpts.colour) query.color = pluginOpts.colour;

  const preroll = pluginOpts.preroll !== videoId && pluginOpts.preroll;
  const postroll = pluginOpts.postroll !== videoId && pluginOpts.postroll;
  const playlistArray = [];
  if (preroll) {
    firstVideoToPlay = preroll;
    playlistArray.push(videoId);
  }
  if (postroll) playlistArray.push(postroll);
  if (playlistArray.length > 0) {
    query.playlist = playlistArray.join(',');
  }

  const queryWithUrlOptions = {
    ...query,
    ...urlOptions,
  };

  if (queryWithUrlOptions.t) {
    queryWithUrlOptions.start = convertToSeconds(queryWithUrlOptions.t);
  }

  /*
   * Generate URL
   */
  return `https://www.youtube-nocookie.com/embed/${firstVideoToPlay}?${queryHashToString(queryWithUrlOptions)}`;
}

function getVideoIdAndAfter(href) {
  const splitBy = ['v=', '/embed/', '://youtu.be/'];
  const splitUsingRegex = new RegExp(splitBy.join('|'), 'i');

  return href.split(splitUsingRegex)[1];
}

/*
 * Load parameters from user's original Youtube URL
 */
export function parseOriginalUrl(href) {
  const videoIdAndAfter = getVideoIdAndAfter(href);

  const [videoId, ...params] = videoIdAndAfter.split(/[&#?]/);

  const queryParams = params.reduce((combined, nextParam) => {
    // Example nextParam: random=string
    const [name, value] = nextParam.split('=');
    // eslint-disable-next-line no-param-reassign
    combined[name] = value;
    return combined;
  }, {});

  return {
    videoId,
    queryParams,
  };
}

function load() {
  findElements('a.lazy-load-youtube').forEach((domItem, index) => {
    const videoLinkElement = domItem;
    const href = videoLinkElement.getAttribute('href');
    const parsedUrl = parseOriginalUrl(href);

    /*
     * Load Youtube ID
     */
    const { videoId, queryParams: urlOptions } = parsedUrl;

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

    videoLinkElement.innerHTML = `<div aria-hidden="true" class="lazy-load-info"><span class="titletext youtube" itemprop="name">${videoTitle()}</span></div>`;

    const lazyloadDiv = createElements('<div aria-hidden="true" class="lazy-load-div"></div>');
    videoLinkElement.insertBefore(lazyloadDiv, videoLinkElement.firstChild);
    if (pluginOptions.buttonstyle) {
      videoLinkElement.classList.add(pluginOptions.buttonstyle);
    }

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
    videoLinkElement.getAttribute('href',
      youtubeUrl(videoId) + (queryHashToString(urlOptions)));

    /*
     * Register "onclick" event handler
     */
    videoLinkElement.addEventListener('click', (event) => {
      const eventTarget = event.currentTarget;
      event.preventDefault();

      if (eventTarget.tagName.toLowerCase() !== 'a') {
        return;
      }

      removePlayerControls(eventTarget);

      /*
       * Generate iFrame
       */
      const videoUrl = getVideoUrl({
        pluginOptions, videoId, urlOptions,
      });

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
