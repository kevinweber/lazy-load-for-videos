export default function findElements(domSelector, rootNode = document) {
    return [].slice.call(rootNode.querySelectorAll(domSelector));
}
