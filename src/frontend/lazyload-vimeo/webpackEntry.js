import '../shared/styles.scss';
import onReady from '../shared-utils/onReady';
import lazyloadVimeo from './lazyloadVimeo';

onReady(() => {
  lazyloadVimeo(window.llvConfig.vimeo);
});
