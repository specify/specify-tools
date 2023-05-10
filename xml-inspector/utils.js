/**
 * Miscallaneous utilities
 */

/**
 * Run checks on the attributes.
 * Quite flexible so can also be used for validating initialize=""
 */
export function validateAttributes(
  warn,
  keys,
  // Callback to get definition for a given property
  getDef,
  // Callback to get a value for a given property
  getValue,
  name = 'attribute',
  context
) {
  Array.from(new Set(keys)).map((attribute) => {
    const definition = getDef(attribute);
    const value = getValue(attribute);

    if (definition === undefined) warn(`Unknown ${name}`, [attribute]);
    else {
      definition.values ??= new Set();
      definition.values.add(value);
      definition.frequencies ??= {};
      const stringValue = value?.toString() ?? '(null)';
      definition.frequencies[stringValue] ??= 0;
      definition.frequencies[stringValue] += 1;
      if (value === null || value === undefined || value === '') {
        if (definition.required !== false)
          warn(`Missing required ${name}`, [attribute]);
      } else {
        const { type = 'string' } = definition;
        if (type === 'string') {
        } else if (type === 'number') {
          const parsed = Number.parseInt(value);
          if (Number.isNaN(parsed)) warn('Invalid number', [attribute, value]);
        } else if (type === 'boolean') {
          const parsed = value === 'true' || value === 'false';
          if (!parsed) warn('Invalid boolean', [attribute, value]);
        }
        definition.validate?.(
          value,
          (msg, parts = []) => warn(msg, [attribute, value, ...parts]),
          context
        );
      }
    }
  });
}

export function parseXml(string) {
  const parsedXml = new window.DOMParser().parseFromString(
    string,
    'text/xml'
  ).documentElement;

  // Chrome, Safari
  const parseError = parsedXml.getElementsByTagName('parsererror')[0];
  if (typeof parseError === 'object')
    return (parseError.children[1].textContent ?? parseError.innerHTML).trim();
  // Firefox
  else if (parsedXml.tagName === 'parsererror')
    return (
      parsedXml.childNodes[0].nodeValue ??
      parsedXml.textContent ??
      parsedXml.innerHTML
    ).trim();
  else return parsedXml;
}
