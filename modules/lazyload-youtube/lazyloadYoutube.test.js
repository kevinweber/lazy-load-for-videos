import lazyloadYoutube, { convertToSeconds, defaultPluginOptions, getVideoUrl, parseOriginalUrl } from './lazyloadYoutube';

/**
 * Missing tests for at least:
 * 
 * pluginOption.buttonstyle
 * pluginOption.responsive
 * pluginOption.thumbnailquality
 * pluginOption.loadthumbnail
 */

const validVideoId = 'ABC123def4g';

function mockVideoUrlInput(override = {}) {
  const videoUrl = getVideoUrl({
    videoId: validVideoId,
    ...override,
    pluginOptions: {
      ...defaultPluginOptions,
      ...override.pluginOptions,
    },
  });

  return {
    url: videoUrl,
    queryParams: new URLSearchParams(videoUrl),
  };
}

describe('convertToSeconds', () => {
  it('correctly parses 0', () => {
    expect(convertToSeconds('0')).toBe(0);
  });

  it('correctly parses 0m20s', () => {
    expect(convertToSeconds('0m20s')).toBe(20);
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

describe('getVideoUrl', () => {
  it('returns default URL with expected query', () => {
    // https://www.youtube.com/watch?v=${validVideoId}
    const mockVideo = mockVideoUrlInput();
    expect(mockVideo.url).toBe(`https://www.youtube.com/embed/${validVideoId}?autoplay=1&rel=0&iv_load_policy=3&color=red`);
  });

  it('supports modestbranding plugin option (default)', () => {
    const mockVideo = mockVideoUrlInput();

    expect(mockVideo.queryParams.get('modestbranding')).toBe(null);
  });

  it('supports modestbranding plugin option (override)', () => {
    const mockVideo = mockVideoUrlInput({
      pluginOptions: {
        modestbranding: true,
      },
    });

    expect(mockVideo.queryParams.get('modestbranding')).toBe('1');
  });

  it('supports modestbranding for URL option', () => {
    const mockVideo = mockVideoUrlInput({
      pluginOptions: {
        modestbranding: false,
      },
      urlOptions: {
        modestbranding: '1',
      },
    });

    expect(mockVideo.queryParams.get('modestbranding')).toBe('1');
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

  it('supports controls plugin option (default)', () => {
    const mockVideo = mockVideoUrlInput();
    expect(mockVideo.queryParams.get('controls')).toBe(null);
  });

  it('supports controls plugin option (override)', () => {
    const mockVideo = mockVideoUrlInput({
      pluginOptions: {
        controls: false,
      },
    });

    expect(mockVideo.queryParams.get('controls')).toBe('0');
  });

  it('supports relations plugin option (default)', () => {
    const mockVideo = mockVideoUrlInput();
    expect(mockVideo.queryParams.get('rel')).toBe('0');
  });

  it('supports relations plugin option (override)', () => {
    const mockVideo = mockVideoUrlInput({
      pluginOptions: {
        relations: false,
      },
    });

    expect(mockVideo.queryParams.get('rel')).toBe(null);
  });

  it('supports rel URL option', () => {
    const mockVideo = mockVideoUrlInput({
      pluginOptions: {
        relations: false,
      },
      urlOptions: {
        rel: '0',
      },
    });

    expect(mockVideo.queryParams.get('rel')).toBe('0');
  });

  it('supports loadpolicy plugin option (default)', () => {
    const mockVideo = mockVideoUrlInput();
    expect(mockVideo.queryParams.get('iv_load_policy')).toBe('3');
  });

  it('supports loadpolicy plugin option (override)', () => {
    const mockVideo = mockVideoUrlInput({
      pluginOptions: {
        loadpolicy: false,
      },
    });

    expect(mockVideo.queryParams.get('iv_load_policy')).toBe(null);
  });
});