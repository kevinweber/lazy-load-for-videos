/* global lazyload_video_settings */
// INFO: "lazyload_video_settings" is added inline into the page using PHP
import jQueryBindFirst from '../utils/jQueryBindFirst';

import onReady from '../utils/onReady';
import lazyloadYoutube from './lazyloadYoutube';

onReady(() => {
  jQueryBindFirst();
  lazyloadYoutube(lazyload_video_settings.youtube);
});
