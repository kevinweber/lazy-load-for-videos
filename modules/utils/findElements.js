export default (domSelector, rootNode = document) => [].slice.call(
  rootNode.querySelectorAll(domSelector),
);
