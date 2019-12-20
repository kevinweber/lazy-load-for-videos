import queryHashToString from './queryHashToString';

describe('queryHashToString', () => {
  it('converts object into string', () => {
    const queryObject = {
      autoplay: 1,
      'something-else': 'it totally works',
    };

    expect(queryHashToString(queryObject)).toBe('autoplay=1&something-else=it totally works');
  });
});
