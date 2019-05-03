/* global lazyload_video_settings */
import './styles.scss';

import jQueryBindFirst from '../utils/jQueryBindFirst';
import onReady from '../utils/onReady';

import lazyloadVimeo from '../lazyload-vimeo/lazyloadVimeo';
import lazyloadYoutube from '../lazyload-youtube/lazyloadYoutube';

onReady(() => {
  jQueryBindFirst(window.jQuery);
  lazyloadVimeo(lazyload_video_settings.vimeo);
  lazyloadYoutube(lazyload_video_settings.youtube);
});
