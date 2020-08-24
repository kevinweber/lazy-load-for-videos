import {
  init,
  resizeVideo,
  setBackgroundImage,
  inViewOnce,
} from '../shared/video';
import createElements from '../shared-utils/createElements';
import findElements from '../shared-utils/findElements';
import queryHashToString from './queryHashToString';

/*
 * Lazy Load Youtube
 * by Kevin Weber (www.kweber.com)
 */

// Classes
const classPreviewYoutube = 'preview-youtube';

let pluginOptions;
export const defaultPluginOptions = {
  colour: 'red', // supported colours: red, white
  controls: true,
  loadpolicy: true,
  buttonstyle: '',
  preroll: '',
  postroll: '',
  thumbnailquality: '0',
  loadthumbnail: true,
  // callback: null, // <- Currently not supported
};

function removePlayerControls(element) {
  element.classList.remove(classPreviewYoutube);
}

export function convertToSeconds(timestring) {
  if (Number(timestring)) return Number(timestring);

  let startTime = 0;
  const timeFactors = [3600, 60, 1]; // h, m, s
  const startMatch = timestring.match(
    /(?:(\d+)(?:h))?(?:(\d+)(?:m))?(?:(\d+)(?:s))?/,
  );

  if (startMatch) {
    for (let s = 1; s < startMatch.length; s += 1) {
      if (typeof startMatch[s] !== 'undefined') {
        startTime += Number(startMatch[s]) * timeFactors[s - 1];
      }
    }
  }

  return startTime;
}

export function getEmbedUrl({
  pluginOptions: pluginOpts,
  videoId,
  urlOptions,
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
  return `https://www.youtube-nocookie.com/embed/${firstVideoToPlay}?${queryHashToString(
    queryWithUrlOptions,
  )}`;
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

/*
 * Generate thumbnail URL from ID
 */
function getThumbnailUrl(thumbnailId) {
  return `https://i2.ytimg.com/vi/${thumbnailId}/${pluginOptions.thumbnailquality}.jpg`;
}

function setBackgroundImg(element) {
  const href = element.getAttribute('href');
  const { videoId } = parseOriginalUrl(href);
  let src = getThumbnailUrl(videoId);

  // Create a temporary image. Once it is loaded, we can update the video element
  // using the src of this temporary image, then remove this temporary image.
  const img = createElements(`<img style="display:none" src="${src}">`)
    .firstChild;

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

function loadVideo(domNode) {
  const videoLinkElement = domNode;
  const href = videoLinkElement.getAttribute('href');
  const parsedUrl = parseOriginalUrl(href);

  /*
   * Load Youtube ID
   */
  const { videoId, queryParams: urlOptions } = parsedUrl;

  function videoTitle() {
    if (videoLinkElement.getAttribute('data-video-title') !== undefined) {
      return videoLinkElement.getAttribute('data-video-title');
    }
    if (videoLinkElement.innerHTML) {
      return videoLinkElement.innerHTML;
    }
    return '';
  }

  videoLinkElement.innerHTML = `<div aria-hidden="true" class="lazy-load-info"><span class="titletext youtube">${videoTitle()}</span></div>`;

  const lazyloadDiv = createElements(
    '<div aria-hidden="true" class="lazy-load-div"></div>',
  );
  videoLinkElement.insertBefore(lazyloadDiv, videoLinkElement.firstChild);
  if (pluginOptions.buttonstyle) {
    videoLinkElement.classList.add(pluginOptions.buttonstyle);
  }

  resizeVideo(videoLinkElement.parentNode);

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
     * Generate iFrame/embed URL
     */
    const embedUrl = getEmbedUrl({
      pluginOptions,
      videoId,
      urlOptions,
    });

    const videoIFrame = createElements(
      `<iframe width="${parseInt(
        videoLinkElement.clientWidth,
        10,
      )}" height="${parseInt(
        videoLinkElement.clientHeight,
        10,
      )}" style="vertical-align:top;" src="${embedUrl}" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>`,
    );

    const { parentNode } = eventTarget;
    if (parentNode) {
      parentNode.replaceChild(videoIFrame, eventTarget);
    }
  });
}

function load({ rootNode, loadthumbnail }) {
  const videoLinkElements = findElements('a.lazy-load-youtube', rootNode);
  videoLinkElements.forEach(loadVideo);

  if (loadthumbnail) {
    inViewOnce(videoLinkElements, (element) => setBackgroundImg(element));
  }
}

function lazyloadYoutube(options) {
  pluginOptions = {
    ...defaultPluginOptions,
    ...options,
  };

  init({
    load,
    pluginOptions,
  });
}

export default lazyloadYoutube;
