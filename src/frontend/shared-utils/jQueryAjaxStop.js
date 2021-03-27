export default (callback) => {
  const $ = window.jQuery || window.$;

  if ($) {
    $(document).ajaxStop(() => {
      callback();
    });
  }
};
