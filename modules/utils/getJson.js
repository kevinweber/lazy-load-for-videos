export default function getJson(url, callback) {
  const request = new XMLHttpRequest();
  request.open('GET', url, true);

  request.onload = () => {
    if (request.status >= 200 && request.status < 400) {
      // Success!
      const data = JSON.parse(request.responseText);
      callback(data);
    }
  };

  request.send();
}
