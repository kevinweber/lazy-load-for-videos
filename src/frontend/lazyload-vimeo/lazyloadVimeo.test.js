import {
  getEmbedUrl, parseOriginalUrl, parseVideoUri, filterDotHash, combineQueryParams,
} from './lazyloadVimeo';

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

  it('returns expected object for URL without dnt query param', () => {
    expect(parseOriginalUrl('https://vimeo.com/456')).toEqual({
      queryParams: {},
    });
  });

  it('returns expected object for URL with dnt=1', () => {
    expect(parseOriginalUrl('https://vimeo.com/456?dnt=1')).toEqual({
      queryParams: {
        dnt: '1',
      },
    });
  });

  it('returns expected object for URL with dnt=0', () => {
    expect(parseOriginalUrl('https://vimeo.com/456?dnt=0')).toEqual({
      queryParams: {
        dnt: '0',
      },
    });
  });
});

describe('parseVideoUri', () => {
  it('returns value if URI is undefined', () => {
    expect(parseVideoUri(undefined)).toEqual({
      hParam: undefined,
    });
  });

  it('returns hParam=null for URI without hParam segment, the most common URI structure', () => {
    expect(parseVideoUri('/videos/123')).toEqual({
      hParam: null,
    });
  });

  it('returns hParam=456 for URI with ":456"', () => {
    expect(parseVideoUri('/videos/123:456')).toEqual({
      hParam: '456',
    });
  });

  it('supports a mix of letters and digits', () => {
    expect(parseVideoUri('/videos/123:4a5b6c')).toEqual({
      hParam: '4a5b6c',
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

describe('filterDotHash', () => {
  it('removes dot from string', () => {
    expect(filterDotHash('aaa.123')).toBe('aaa123');
  });

  it('removes hash from string', () => {
    expect(filterDotHash('#00c684')).toBe('00c684');
  });

  it('returns original string if length=0', () => {
    expect(filterDotHash('')).toBe('');
  });
});

describe('combineQueryParams', () => {
  it('adds autoplay=1 by default', () => {
    expect(combineQueryParams({})).toHaveProperty('autoplay', 1);
  });

  it('passes through query params', () => {
    const params = { test: 1 };
    expect(combineQueryParams({ queryParams: params })).toMatchObject(params);
  });

  it('sets color when playercolour option is provided', () => {
    expect(combineQueryParams({ pluginOptions: { playercolour: 'blue' } })).toHaveProperty('color', 'blue');
  });

  it('adds dnt=1 by default', () => {
    expect(combineQueryParams({})).toHaveProperty('dnt', 1);
  });

  it('sets dnt=0 when cookies option is true', () => {
    expect(combineQueryParams({ pluginOptions: { cookies: true } })).toHaveProperty('dnt', 0);
  });

  it('sets dnt=1 when cookies option is false', () => {
    expect(combineQueryParams({ pluginOptions: { cookies: false } })).toHaveProperty('dnt', 1);
  });
});
