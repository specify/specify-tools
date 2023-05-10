import { definitions } from './definitions.js';
import { parseXml, validateAttributes } from './utils.js';
import { sourcePath, files } from './config.js';

/**
 * Gather all files and analyze each
 */
export async function execute() {
  const fileContents = await Promise.all(
    files.map(async (file) => [
      file,
      // Error prone path concatenation, but simple
      await fetch(`${sourcePath}${file}`)
        .then((response) => response.text())
        .then(parseXml),
    ])
  );
  fileContents.forEach(([name, document]) =>
    analyze([name, [document.tagName, document]], definitions, document)
  );
}

/**
 * Resolve the switch statements in the definition to a single definition.
 * @remarks
 * Supports nested switches
 */
function parseSwitchDefinitions(node, definition) {
  // Can remap the value that will be used as a condition
  const condition = definition.switchMapper?.(node) ?? node;

  const switchDefinition =
    definition.switch?.find((node) =>
      // Can define a custom comparison function
      typeof node.condition === 'function'
        ? node.condition(condition)
        : node.condition === condition
    ) ?? {};

  // Support nested switches
  const nestedSwitches =
    switchDefinition.switch === undefined
      ? {}
      : parseSwitchDefinitions(node, switchDefinition);

  return {
    ...definition,
    ...switchDefinition,
    ...nestedSwitches,
    attributes: {
      ...definition.attributes,
      ...switchDefinition.attributes,
      ...nestedSwitches.attributes,
    },
    children: {
      ...definition.children,
      ...switchDefinition.children,
      ...nestedSwitches.children,
    },
  };
}

/**
 * Collect all warnings here so that can group them together afterward and print
 * them all in a nice tree-like structure
 */
const warns = [];

export function formatWarns() {
  const indexed = {};
  warns.map(([msg, path]) => {
    indexed[msg] ??= {};

    const lastPart = path.at(-1);
    const lastValue =
      Array.isArray(lastPart) && lastPart.length === 2 ? lastPart[0] : lastPart;
    indexed[msg][lastValue] ??= [];
    indexed[msg][lastValue].push({
      /**
       * When adding elements to path, could add a 2 item array where first item
       * is a string and second item is an object. The string will be used in a
       * string-only representation of the path, and the object will be used in
       * a more structured representation of the path.
       *
       * Useful if converting the output to JSON or serializing in other way
       */
      string: path
        .map((value) =>
          Array.isArray(value) && value.length === 2 ? value[0] : value
        )
        .join(' > '),
      obj: path
        .filter((value) => Array.isArray(value) && value.length === 2)
        .map(([_, object]) => object),
    });
  });
  return indexed;
}

/**
 * Recursively compare the node tree to definition tree
 */
function analyze(commentPath, rawDefinition, node) {
  const definitions = parseSwitchDefinitions(node, rawDefinition);
  const warn = (message, parts = []) =>
    warns.push([message, [...commentPath, ...parts]]);

  definitions.validate?.(node, warn, node);

  validateAttributes(
    warn,
    [
      ...Object.keys(definitions.attributes ?? {}),
      ...Array.from(node.attributes).map((v) => v.name),
    ],
    (name) => definitions.attributes[name],
    (name) => node.getAttribute(name),
    'attribute',
    node
  );

  /**
   * Order of children between different tag-names does not matter, but order
   * within a given tag name does, thus can group children by tag name to
   * simplify work
   */
  const grouped = Array.from(node.children).reduce((grouped, node) => {
    grouped[node.tagName] ??= [];
    grouped[node.tagName].push(node);
    return grouped;
  }, {});

  const tags = Array.from(
    new Set([
      ...Object.keys(grouped),
      ...Object.keys(definitions.children ?? []),
    ])
  );
  tags.map((tagName) => {
    const rawDefinition = definitions.children?.[tagName];
    const definition =
      typeof rawDefinition === 'function' ? rawDefinition() : rawDefinition;
    if (definition === undefined) warn('Unknown tag', [tagName]);
    else if (grouped[tagName] === undefined) {
      if (definition.required !== false) warn('Missing tag', [tagName]);
    } else {
      grouped[tagName].map((node, index) =>
        analyze([...commentPath, [tagName, node], index], definition, node)
      );
    }
  });

  if (tags.length === 0) {
    rawDefinition.text ??= { required: false };
    validateAttributes(
      warn,
      ['text'],
      () => rawDefinition.text,
      () => node.textContent,
      'content',
      node
    );
  }
}
