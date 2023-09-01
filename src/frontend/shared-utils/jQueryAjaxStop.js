export default function jQueryAjaxStop(callback) {
  const $ = window.jQuery || window.$;

  if ($ && typeof $.ajaxStop === 'function') {
    $(document).ajaxStop(callback);
  }
}
