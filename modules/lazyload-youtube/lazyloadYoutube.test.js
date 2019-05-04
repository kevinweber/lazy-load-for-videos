import lazyloadYoutube, { defaultPluginOptions, getVideoUrl } from './lazyloadYoutube';

const validVideoId = 'ABC123def4g';

const mockSettings = {
  youtube: {},
};

// TODO
describe('getVideoUrl', () => {
  it('returns default URL without any overrides', () => {
    // https://www.youtube.com/watch?v=${validVideoId}
    const videoUrl = getVideoUrl({ pluginOptions: defaultPluginOptions, videoId: validVideoId});
    expect(videoUrl).toBe(`https://www.youtube.com/embed/${validVideoId}?autoplay=1`);
  });

  it('supports modestbranding for general override', () => {
    // https://www.youtube.com/watch?v=${validVideoId}&modestbranding=1
    const videoUrl = getVideoUrl({ pluginOptions: {
      ...defaultPluginOptions,
      modestbranding: true,
    }, videoId: validVideoId });
    expect(videoUrl).toBe(`https://www.youtube.com/embed/${validVideoId}?modestbranding=1&autoplay=1`);
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