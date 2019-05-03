/* global lazyload_video_settings */
import jQueryBindFirst from '../utils/jQueryBindFirst';

import onReady from '../utils/onReady';
import lazyloadYoutube from './lazyloadYoutube';

onReady(() => {
  jQueryBindFirst(window.jQuery);
  lazyloadYoutube(lazyload_video_settings.youtube);
});
