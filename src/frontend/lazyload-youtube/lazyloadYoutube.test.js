import {
  convertToSeconds,
  defaultPluginOptions,
  getEmbedUrl,
  parseOriginalUrl,
} from './lazyloadYoutube';

/**
 * Missing tests for at least:
 *
 * pluginOption.buttonstyle
 * pluginOption.thumbnailquality
 * pluginOption.loadthumbnail
 */

const fakeValidVideoId = 'ABC123def4g';
const fakePrerollVideoId = 'DEF567def8g';
const fakePostrollVideoId = 'GHI901def2g';

function mockVideoUrlInput(override = {}) {
  const videoUrl = getEmbedUrl({
    videoId: fakeValidVideoId,
    ...override,
    pluginOptions: {
      ...defaultPluginOptions,
      ...override.pluginOptions,
    },
  });

  return {
    url: videoUrl,
    queryParams: new URLSearchParams(videoUrl),
    videoId: parseOriginalUrl(videoUrl).videoId,
  };
}

describe('convertToSeconds', () => {
  it('correctly parses 0', () => {
    expect(convertToSeconds('0')).toBe(0);
  });

  it('correctly parses 200 (without "s")', () => {
    expect(convertToSeconds('200')).toBe(200);
  });

  it('correctly parses 20s', () => {
    expect(convertToSeconds('20s')).toBe(20);
  });

  it('correctly parses 1m20s', () => {
    expect(convertToSeconds('1m20s')).toBe(80);
  });

  it('correctly parses 1h1m20s', () => {
    expect(convertToSeconds('1h1m20s')).toBe(3680);
  });

  it('correctly parses 1h20s', () => {
    expect(convertToSeconds('1h20s')).toBe(3620);
  });
});

describe('parseOriginalUrl', () => {
  it('correctly parses https://www.youtube.com/watch?v=aaa', () => {
    const url = 'https://www.youtube.com/watch?v=IJNR2EpS0jw&modestbranding=1&random=string';
    const parsedUrl = parseOriginalUrl(url);

    expect(parsedUrl.videoId).toBe('IJNR2EpS0jw');
    expect(parsedUrl.queryParams).toEqual({
      modestbranding: '1',
      random: 'string',
    });
  });

  it('correctly parses https://www.youtube.com/embed/aaa', () => {
    const url = 'https://www.youtube.com/embed/IJNR2EpS0jw?modestbranding=1&random=string';
    const parsedUrl = parseOriginalUrl(url);

    expect(parsedUrl.videoId).toBe('IJNR2EpS0jw');
    expect(parsedUrl.queryParams).toEqual({
      modestbranding: '1',
      random: 'string',
    });
  });

  it('correctly parses https://www.youtube-nocookie.com/embed/aaa', () => {
    const url = 'https://www.youtube-nocookie.com/embed/IJNR2EpS0jw?modestbranding=1&random=string';
    const parsedUrl = parseOriginalUrl(url);

    expect(parsedUrl.videoId).toBe('IJNR2EpS0jw');
    expect(parsedUrl.queryParams).toEqual({
      modestbranding: '1',
      random: 'string',
    });
  });

  it('correctly parses http://youtu.be/aaa', () => {
    const url = 'http://youtu.be/IJNR2EpS0jw?modestbranding=1&random=string';
    const parsedUrl = parseOriginalUrl(url);

    expect(parsedUrl.videoId).toBe('IJNR2EpS0jw');
    expect(parsedUrl.queryParams).toEqual({
      modestbranding: '1',
      random: 'string',
    });
  });
});

describe('getEmbedUrl', () => {
  it('returns default URL with expected query', () => {
    // https://www.youtube-nocookie.com/watch?v=${fakeValidVideoId}
    const mockVideo = mockVideoUrlInput();
    expect(mockVideo.url).toBe(
      `https://www.youtube-nocookie.com/embed/${fakeValidVideoId}?autoplay=1&modestbranding=1&rel=0&iv_load_policy=3&color=red`,
    );
  });

  it('supports colour plugin option', () => {
    const mockVideo = mockVideoUrlInput({
      pluginOptions: {
        colour: 'white',
      },
    });

    expect(mockVideo.queryParams.get('color')).toBe('white');
  });

  it('supports color URL option', () => {
    const mockVideo = mockVideoUrlInput({
      pluginOptions: {
        colour: 'white',
      },
      urlOptions: {
        color: 'red',
      },
    });

    expect(mockVideo.queryParams.get('color')).toBe('red');
  });

  it('supports controls plugin option', () => {
    const mockVideo = mockVideoUrlInput({
      pluginOptions: {
        controls: false,
      },
    });

    expect(mockVideo.queryParams.get('controls')).toBe('0');
  });

  it('supports loadpolicy plugin option', () => {
    const mockVideo = mockVideoUrlInput({
      pluginOptions: {
        loadpolicy: false,
      },
    });

    expect(mockVideo.queryParams.get('iv_load_policy')).toBe(null);
  });

  it('supports preroll plugin option', () => {
    const mockVideo = mockVideoUrlInput({
      pluginOptions: {
        preroll: fakePrerollVideoId,
      },
    });

    expect(mockVideo.videoId).toBe(fakePrerollVideoId);
    expect(mockVideo.queryParams.get('playlist')).toBe(fakeValidVideoId);
  });

  it('supports postroll plugin option', () => {
    const mockVideo = mockVideoUrlInput({
      pluginOptions: {
        postroll: fakePostrollVideoId,
      },
    });

    expect(mockVideo.videoId).toBe(fakeValidVideoId);
    expect(mockVideo.queryParams.get('playlist')).toBe(fakePostrollVideoId);
  });

  it('supports preroll combined with postroll plugin option', () => {
    const mockVideo = mockVideoUrlInput({
      pluginOptions: {
        preroll: fakePrerollVideoId,
        postroll: fakePostrollVideoId,
      },
    });

    expect(mockVideo.videoId).toBe(fakePrerollVideoId);
    expect(mockVideo.queryParams.get('playlist')).toBe(
      `${fakeValidVideoId},${fakePostrollVideoId}`,
    );
  });
});
