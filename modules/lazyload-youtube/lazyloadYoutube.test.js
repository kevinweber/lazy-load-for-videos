import lazyloadYoutube, { defaultPluginOptions, getVideoUrl } from './lazyloadYoutube';

const validVideoId = 'ABC123def4g';

const mockSettings = {
  youtube: {},
};

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

// TODO
// autoplay=1${colour}${controls}${loadpolicy}
// ${modestbranding}${relations}${playlist}${embedstart}

describe('getVideoUrl', () => {
  it('returns default URL without any overrides', () => {
    // https://www.youtube.com/watch?v=${validVideoId}
    const mockVideo = mockVideoUrlInput();
    expect(mockVideo.url).toBe(`https://www.youtube.com/embed/${validVideoId}?autoplay=1&rel=0&controls=0&iv_load_policy=3`);
  });

  it('supports modestbranding for general override', () => {
    const mockVideo = mockVideoUrlInput({
      pluginOptions: {
        modestbranding: true,
      }
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