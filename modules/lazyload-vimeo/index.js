/* global lazyload_video_settings */
import jQueryBindFirst from '../utils/jQueryBindFirst';

import onReady from '../utils/onReady';
import lazyloadVimeo from './lazyloadVimeo';

export default function () {
  onReady(() => {
    jQueryBindFirst(window.jQuery);
    lazyloadVimeo(lazyload_video_settings.vimeo);
  });
}
