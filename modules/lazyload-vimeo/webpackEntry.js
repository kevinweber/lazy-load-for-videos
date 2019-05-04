/* global lazyload_video_settings */
// INFO: "lazyload_video_settings" is added inline into the page using PHP
import jQueryBindFirst from '../utils/jQueryBindFirst';

import onReady from '../utils/onReady';
import lazyloadVimeo from './lazyloadVimeo';

onReady(() => {
  jQueryBindFirst();
  lazyloadVimeo(lazyload_video_settings.vimeo);
});
