import { execute, formatWarns } from './execute.js';
import { parseUrl } from './queryString.js';

const { schema } = parseUrl();

if (schema === undefined)
  import('./missingSchemaArgument.js').then(({ run }) => run());
else {
  const { definitions } = await import(`./schema/${schema}/definitions.js`);
  const { sourcePath, files } = await import(`./schema/${schema}/config.js`);
  execute(definitions, sourcePath, files).then(() => {
    console.log('Definitions', definitions);
    console.warn('Warnings', formatWarns());
  });
}
