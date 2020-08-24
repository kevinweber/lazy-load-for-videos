import '../shared/styles.scss';
import onReady from '../shared-utils/onReady';
import lazyloadYoutube from './lazyloadYoutube';

onReady(() => {
  lazyloadYoutube(window.llvConfig.youtube);
});
