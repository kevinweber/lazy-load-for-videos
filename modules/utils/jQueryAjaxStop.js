export default function (callback) {
  const $ = window.jQuery || window.$;

  if ($) {
    $(document).ajaxStop(() => {
      callback();
    });
  }
}
