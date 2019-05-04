import './styles.scss';
import './tooltips.scss';

const $ = window.jQuery || window.$;

/*
 * Change URL when tab is clicked
 */
const handleTabsUrl = () => {
  $('#tabs').on('tabsactivate', (event, ui) => {
    const href = ui.newTab.children('li a').first().attr('href');
    window.history.pushState(null, null, href);
    if (window.history.pushState) {
      window.history.pushState(null, null, href);
    } else {
      window.location.hash = href;
    }
  });
};

/*
 * When user calls a URL that contains a hash, scroll to top
 */
const handleTabsUrlScrollTop = () => {
  setTimeout(() => {
    if (window.location.hash) {
      $('html, body').animate({
        scrollTop: 0,
      }, 1000);
    }
  }, 1);
};

/*
 * Handle jQuery tabs
 */
const handleTabs = () => {
  $('#tabs').tabs();

  handleTabsUrl();
  handleTabsUrlScrollTop();
};

const toggle = () => {
  $('.toggle').on('click', (e) => {
    $(e.target).siblings('.toggle-me').toggle();
  });
};

const addColourPicker = () => {
  $('.ll_picker_player_colour').wpColorPicker();
};

const init = () => {
  handleTabs();
  addColourPicker();
  toggle();
};

$(document).ready(() => {
  init();
});
