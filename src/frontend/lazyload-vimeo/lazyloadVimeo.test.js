import { getEmbedUrl, parseOriginalUrl } from './lazyloadVimeo';

describe('parseOriginalUrl', () => {
  it('returns expected object for URL without search', () => {
    expect(parseOriginalUrl('https://vimeo.com/456')).toEqual({
      queryParams: {},
    });
  });

  it('returns expected object for URL with search', () => {
    expect(parseOriginalUrl('https://vimeo.com/456?dnt=1&app_id=123')).toEqual({
      queryParams: {
        dnt: '1', app_id: '123',
      },
    });
  });
});

describe('getEmbedUrl', () => {
  it('returns default URL with expected query', () => {
    expect(getEmbedUrl({
      videoId: '526338719',
      queryParams: {
        dnt: '1', app_id: '123',
      },
    })).toBe(
      'https://player.vimeo.com/video/526338719?dnt=1&app_id=123',
    );
  });
});
