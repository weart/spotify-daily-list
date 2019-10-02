import SubmissionError from '../error/SubmissionError';
import { ENTRYPOINT } from '../config/entrypoint';

const MIME_TYPE = 'application/ld+json';

// make query string array of values
const makeParamArray = (key, arr) => arr.map(val => `${key}[]=${val}`).join('&');

export default function(id, options = {}) {
  console.log('fetching', id);
  if (typeof options.headers === 'undefined') Object.assign(options, { headers: new Headers() });

  if (options.headers.get('Accept') === null) options.headers.set('Accept', MIME_TYPE);

  if (
    options.body !== undefined &&
    !(options.body instanceof FormData) &&
    options.headers.get('Content-Type') === null
  ) {
    options.headers.set('Content-Type', MIME_TYPE);
  }

  if (options.params) {
    var queryString = Object.keys(options.params)
      .map(key =>
        Array.isArray(options.params[key])
          ? makeParamArray(key, options.params[key])
          : `${key}=${options.params[key]}`,
      )
      .join('&');
    id = `${id}?${queryString}`;
  }

  /* enable CORS for all requests
  Object.assign(options, {
    mode: 'cors',
    credentials: 'include',
  });
  */

  const entryPoint = ENTRYPOINT + (ENTRYPOINT.endsWith('/') ? '' : '/');

  return fetch(new URL(id, entryPoint), options).then(response => {
    console.log('fetched', id, entryPoint);
    if (response.ok) return response;

    return response.json().then(json => {
      const error = json['hydra:description']
        ? json['hydra:description']
        : response.statusText;
      if (!json.violations) throw Error(error);

      const errors = { _error: error };
      json.violations.map(violation =>
        Object.assign(errors, { [violation.propertyPath]: violation.message }),
      );

      throw new SubmissionError(errors);
    });
  });
}
