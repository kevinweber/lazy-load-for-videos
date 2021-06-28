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
 * Handle jQuery tabs
 */
const handleTabs = () => {
  $('#tabs').tabs();
  handleTabsUrl();
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
// EJ add new tabs code
$(document).ready(() => {
  $('li').click(function addActive() {
      $('li.nav-tab.nav-tab-active').removeClass('nav-tab-active');
      $(this).addClass('nav-tab-active');
  });
});
