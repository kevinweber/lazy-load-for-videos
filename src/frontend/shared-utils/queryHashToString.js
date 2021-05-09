export default function queryHashToString(queryObject) {
  return Object.keys(queryObject)
    .map((key) => `${key}=${queryObject[key]}`)
    .join('&');
}
