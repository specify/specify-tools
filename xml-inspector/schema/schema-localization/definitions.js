/**
 * Form schema and validation rules are defined here
 */

import { tables } from '../tables.js';

const validate = {
  table(value, warn) {
    if (!tables.includes(value.toLowerCase())) warn('Unknown table name');
  },
  list: (list) => (value, warn) => {
    if (!list.includes(value))
      warn(`Value not among available options: ${list.join(', ')}`);
  },
};

const container = {
  children: {
    str: {
      required: false,
      switch: [
        {
          condition: (node) => node.hasAttribute('reference'),
          attributes: {
            reference: {},
          },
        },
        {
          condition: () => true,
          attributes: {
            language: {
              validate: validate.list(['en', 'uk', 'pt', 'ru']),
            },
            country: {
              required: false,
              validate: validate.list(['UA', 'BR', 'RU']),
            },
            variant: {
              required: false,
              validate: validate.list(['UA', 'BR', 'RU']),
            },
          },
          pairs: new Set(),
          validate(node) {
            if (this.pairs.size === 0)
              console.log('Language pairs', this.pairs);
            this.pairs.add(
              [
                node.getAttribute('language') ?? '',
                node.getAttribute('country') ?? '',
                node.getAttribute('variant') ?? '',
              ].join('_')
            );
          },
          children: {
            version: {
              text: {
                type: 'number',
                validate: validate.list([0]),
              },
            },
            text: {},
            itemName: {
              required: false,
              attributes: {
                reference: {},
              },
            },
            itemDesc: {
              required: false,
              attributes: {
                reference: {},
              },
            },
            containerName: {
              required: false,
              attributes: {
                class: {},
                reference: {},
              },
            },
            containerDesc: {
              required: false,
              attributes: {
                class: {},
                reference: {},
              },
            },
          },
        },
      ],
    },
  },
};

export const definitions = {
  attributes: {},
  children: {
    container: {
      attributes: {
        name: {
          validate: validate.table,
        },
        isHidden: {
          type: 'boolean',
        },
        isUIFormatter: {
          type: 'boolean',
          required: false,
          default: false,
        },
        format: {
          required: false,
        },
      },
      children: {
        version: {
          text: {
            type: 'number',
            validate: validate.list([0]),
          },
        },
        isSystem: {
          text: {
            type: 'boolean',
          },
        },
        schemaType: {
          text: {
            /*
             * This tag is required in sp6.
             * Default value differs based on where in the code it's used
             */
            type: 'number',
            validate: validate.list([
              // Core schema
              0,
              // WorkBench schema. Yet, it's never used in the XML
              1,
            ]),
          },
        },
        items: {
          children: {
            desc: {
              required: false,
              attributes: {
                name: {},
                type: {
                  required: false,
                  validate: validate.list([
                    'java.lang.String',
                    'ManyToOne',
                    'text',
                    'java.lang.Boolean',
                    'java.util.Calendar',
                    'java.sql.Timestamp',
                    'java.lang.Integer',
                    'java.lang.Float',
                    'OneToMany',
                    'ManyToMany',
                    'java.lang.Short',
                    'java.lang.Double',
                    'java.lang.Byte',
                    'java.util.Date',
                    'java.math.BigDecimal',
                  ]),
                },
                isHidden: {
                  type: 'boolean',
                  required: false,
                  default: false,
                },
                isUIFormatter: {
                  type: 'boolean',
                  default: false,
                  required: false,
                },
                isRequired: {
                  type: 'boolean',
                },
                pickListName: {
                  required: false,
                },
                format: {
                  required: false,
                },
              },
              children: {
                version: {
                  text: {
                    type: 'number',
                    validate: validate.list([0]),
                  },
                },
                isSystem: {
                  text: {
                    type: 'boolean',
                  },
                },
                names: container,
                descs: container,
                /*
                 * Seems like it was used at some point, but is now commented
                 * out in the code
                 */
                spExportSchemaItems: {
                  required: false,
                },
              },
            },
          },
        },
        names: container,
        descs: container,
        aggregator: {
          required: false,
          text: {},
        },
      },
    },
  },
};
