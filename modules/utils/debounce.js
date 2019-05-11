export default function debounce(callback, wait, ...args) {
  let timeout = null;

  return () => {
    const callNow = !timeout;
    const next = () => callback.apply(this, args);

    clearTimeout(timeout);
    timeout = setTimeout(next, wait);

    if (callNow) {
      next();
    }
  };
}
