export default function jQueryAjaxStop(callback) {
  const $ = window.jQuery || window.$;

  if ($) {
    $(document).ajaxStop(() => {
      callback();
    });
  }
}
