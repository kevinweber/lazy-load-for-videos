/**
 * Create an HTML element from an element-like string
 * @param {string} htmlString
 */
export default function createElement(htmlString) {
  const fragment = document.createDocumentFragment();

  const wrapperElement = document.createElement('div');
  wrapperElement.innerHTML = htmlString;

  while (wrapperElement.childNodes[0]) {
    fragment.appendChild(wrapperElement.childNodes[0]);
  }

  return fragment;
}
