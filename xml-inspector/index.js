import { definitions } from './definitions.js';
import { execute, formatWarns } from './execute.js';

execute().then(() => {
  console.log('Definitions', definitions);
  console.warn('Warnings', formatWarns());
});
