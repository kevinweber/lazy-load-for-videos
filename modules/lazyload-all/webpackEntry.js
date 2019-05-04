/* global lazyload_video_settings */
// INFO: "lazyload_video_settings" is added inline into the page using PHP
import './styles.scss';

import jQueryBindFirst from '../utils/jQueryBindFirst';
import onReady from '../utils/onReady';

import lazyloadVimeo from '../lazyload-vimeo/lazyloadVimeo';
import lazyloadYoutube from '../lazyload-youtube/lazyloadYoutube';

onReady(() => {
  jQueryBindFirst();
  lazyloadVimeo(lazyload_video_settings.vimeo);
  lazyloadYoutube(lazyload_video_settings.youtube);
});
