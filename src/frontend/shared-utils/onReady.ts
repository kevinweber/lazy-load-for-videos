export default function onReady(callback: () => void) {
  // If the DOM is already parsed, we can't rely on the DOMContentLoaded event
  // => Call the callback right away
  if (
    document.readyState === 'complete' ||
    document.readyState === 'interactive'
  ) {
    callback();
  } else {
    document.addEventListener('DOMContentLoaded', callback);
  }
}
