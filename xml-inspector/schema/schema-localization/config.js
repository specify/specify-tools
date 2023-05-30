/**
 * Relative path from current directory to directory that contains
 * schema xml files
 */
export const sourcePath = './';

/**
 * Schema definitions to check in the basePath directory
 * @remarks
 * Paths must not start with ./ or /
 */
export const files = [
  ...`
    ./insect/schema_localization.xml
    ./mammal/schema_localization.xml
    ./paleobotany/schema_localization.xml
    ./schema_localization.xml
    ./herpetology/schema_localization.xml
    ./botany/schema_localization.xml
    ./vertpaleo/schema_localization.xml
    ./bird/schema_localization.xml
    ./invertebrate/schema_localization.xml
    ./invertpaleo/schema_localization.xml
    ./fungi/schema_localization.xml
    ./fish/schema_localization.xml
  `
    .trim()
    .split('\n')
    .map((v) => `../../specify6/config/${v.trim().replaceAll('./', '')}`),
];
