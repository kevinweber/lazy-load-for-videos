import lazyloadYoutube, { convertToSeconds, defaultPluginOptions, getVideoUrl, parseOriginalUrl } from './lazyloadYoutube';

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
  it('returns default URL without any overrides', () => {
    // https://www.youtube.com/watch?v=${validVideoId}
    const mockVideo = mockVideoUrlInput();
    expect(mockVideo.url).toBe(`https://www.youtube.com/embed/${validVideoId}?autoplay=1&rel=0&iv_load_policy=3`);
  });

  it('supports modestbranding for general override', () => {
    const mockVideo = mockVideoUrlInput({
      pluginOptions: {
        modestbranding: true,
      },
    });

    expect(mockVideo.queryParams.get('modestbranding')).toBe('1');
  });

  // it('supports modestbranding for link-specific override', () => {
  //   // https://www.youtube.com/watch?v=${validVideoId}
  //   const videoUrl = getVideoUrl({ pluginOptions: defaultPluginOptions, videoId: validVideoId });
  //   expect(videoUrl).toBe(`https://www.youtube.com/embed/${validVideoId}?modestbranding=1&autoplay=1&playlist=${validVideoId}`);
  // });

  // it('supports modestbranding for link-specific override and general override', () => {
  //   // https://www.youtube.com/watch?v=${validVideoId}&modestbranding=1
  //   const videoUrl = getVideoUrl({ pluginOptions: defaultPluginOptions, videoId: validVideoId });
  //   expect(videoUrl).toBe(`https://www.youtube.com/embed/${validVideoId}?modestbranding=1&autoplay=1&playlist=${validVideoId}`);
  // });
});