/**
 * Parse or unparse URL Query parameters
 */

export function formatUrl(url = getUrl(), parameters) {
  const urlObject = new URL(url, getUrl());
  urlObject.search = new URLSearchParams({
    ...Object.fromEntries(urlObject.searchParams),
    ...Object.fromEntries(
      Object.entries(parameters)
        .map(([key, value]) =>
          value === undefined || value === null
            ? undefined
            : [key, value.toString()]
        )
        .filter(Boolean)
    ),
  }).toString();
  return urlObject.toString();
}

const getUrl = () => globalThis.location?.href ?? 'http://localhost/';

export const parseUrl = (url = getUrl()) =>
  Object.fromEntries(new URL(url, getUrl()).searchParams);
