/**
 * Form schema and validation rules are defined here
 */

import { tables } from './tables.js';
import { validateAttributes } from './utils.js';

const validate = {
  class(value, warn) {
    const start = 'edu.ku.brc.specify.datamodel.';
    if (!value.startsWith(value)) warn('Unknown class name');
    else {
      const table = value.slice(start.length);
      if (!tables.includes(table)) warn('Unknown table name');
    }
  },
  init(definition) {
    return {
      required: false,
      definition,
      validate: validate.initialize,
    };
  },
  initialize(value, warn) {
    const parts = value.split(';');
    const definition = this.definition;
    const indexed = Object.fromEntries(
      parts
        .map((part) => {
          const [name, ...values] = part.split('=');
          const value = values.join('=');
          return [name, value];
        })
        .filter(([name]) => name !== '')
    );
    validateAttributes(
      warn,
      [...Object.keys(indexed), ...Object.keys(definition)],
      (name) => definition[name],
      (name) => indexed[name],
      'property'
    );
  },
  list: (list) => (value, warn) => {
    if (!list.includes(value))
      warn(`Value not among available options: ${list.join(', ')}`);
  },
};

const commonInit = {
  title: { required: false },
  align: {
    required: false,
    validate: validate.list(['left', 'right', 'center']),
  },
  fg: {
    required: false,
  },
  editoncreate: {
    required: false,
    type: 'boolean',
  },
};

const pluginInit = (init) =>
  validate.init({
    ...commonInit,
    ...init,
    // Disable plugin if cell with this id has no value
    watch: {
      type: 'number',
      required: false,
    },
    name: {
      required: true,
      validate: validate.list([
        'LatLonUI',
        'PartialDateUI',
        'CollectionRelOneToManyPlugin',
        'ColRelTypePlugin',
        'LocalityGeoRef',
        'LocalityGoogleEarth',
        'WebLinkButton',
        'AttachmentPlugin',
        'HostTaxonPlugin',
        'PaleoMap',
        'ContainersColObjPlugin',
        'TaxonLabelFormatting',
        'ContainerPlugin',
        'DefItemEditorPlugin',
        'LocalityWorldWind',
        'PasswordStrengthUI',
      ]),
    },
  });

const cell = {
  required: false,
  switchMapper: (node) => node.getAttribute('type'),
  attributes: {
    type: {},
    id: {
      required: false,
    },
    rows: {
      required: false,
    },
    visible: {
      required: false,
    },
    colspan: {
      type: 'number',
      required: false,
    },
    cols: {
      type: 'number',
      required: false,
    },
    invisible: {
      type: 'boolean',
      required: false,
    },
    ignore: {
      type: 'boolean',
      required: false,
    },
    initialize: validate.init({
      ...commonInit,
    }),
  },
  switch: [
    {
      condition: 'label',
      attributes: {
        labelfor: {
          required: false,
        },
        label: {
          required: false,
        },
      },
    },
    {
      condition: (value) => value === 'icontype' || value === 'iconview',
      attributes: {
        name: {},
        viewname: {},
        desc: {},
      },
    },
    {
      condition: 'separator',
      attributes: {
        label: {
          required: false,
        },
        icon: {
          required: false,
        },
        forclass: {
          required: false,
          validate: validate.class,
        },
      },
    },
    {
      condition: 'subview',
      attributes: {
        name: {},
        defaulttype: {
          validate: validate.list(['form', 'table', 'icon']),
          required: false,
        },
        label: { required: false },
        rows: {
          type: 'number',
          required: false,
        },
        viewname: {
          required: false,
        },
        valtype: {
          required: false,
          validate: validate.list(['Changed', 'Focus']),
        },
        readonly: {
          type: 'boolean',
          required: false,
        },
        desc: {
          required: false,
        },
        initialize: validate.init({
          btn: {
            type: 'boolean',
            required: false,
          },
          icon: {
            required: false,
          },
          name: { required: false },
          // Help context
          hc: {
            required: false,
          },
          sortfield: {
            required: false,
          },
          many: {
            type: 'boolean',
            required: false,
          },
          addsearch: {
            type: 'boolean',
            required: false,
          },
          ...commonInit,
        }),
      },
    },
    {
      condition: 'panel',
      attributes: {
        coldef: {
          required: false,
        },
        colspan: {
          type: 'number',
          required: false,
        },
        rowdef: {
          required: false,
        },
        // Not used by sp6 or sp7
        name: {
          type: 'string',
          required: false,
        },
      },
      children: {
        rows: {
          required: false,
          attributes: {
            condition: { required: false },
            coldef: { required: false },
          },
          children: {
            row: {
              children: {
                cell: () => cell,
              },
            },
          },
        },
      },
    },
    {
      condition: 'command',
      attributes: {
        name: {
          validate: validate.list([
            'generateLabelBtn',
            'ShowLoansBtn',
            'ReturnLoan',
          ]),
        },
        commandtype: {
          required: false,
          validate: validate.list(['Interactions', 'App', 'ClearCache']),
        },
        vis: {
          type: 'boolean',
          required: false,
        },
        action: {
          required: false,
          validate: validate.list(['ReturnLoan']),
        },
        initialize: validate.init({
          ...commonInit,
          visible: { required: false },
        }),
        label: {
          required: false,
        },
        default: {
          type: 'boolean',
          required: false,
        },
      },
    },
    {
      condition: 'blank',
    },
    {
      condition: 'field',
      attributes: {
        name: {},
        uitype: { required: false },
        isrequired: {
          type: 'boolean',
          required: false,
        },
        valtype: {
          required: false,
          validate: validate.list(['Changed', 'Focus']),
        },
        readonly: {
          type: 'boolean',
          required: false,
        },
        default: {
          required: false,
        },
      },
      switchMapper: (node) => node.getAttribute('uitype') ?? 'text',
      switch: [
        {
          condition: 'querycbx',
          attributes: {
            initialize: validate.init({
              ...commonInit,
              name: { required: false },
              searchview: { required: false },
              displaydlg: { required: false },
              searchdlg: { required: false },
              clonebtn: { type: 'boolean', required: false },
              editbtn: { type: 'boolean', required: false },
              newbtn: { type: 'boolean', required: false },
              // Help context
              hc: {
                required: false,
              },
            }),
          },
        },
        {
          condition: 'list',
          attributes: {
            dsptype: {},
            initialize: validate.init({
              rows: { type: 'number' },
            }),
          },
        },
        {
          condition: 'checkbox',
          attributes: {
            initialize: validate.init({
              ...commonInit,
              visible: {
                type: 'boolean',
                required: false,
              },
              editable: {
                type: 'boolean',
                required: false,
              },
            }),
            label: {
              required: false,
            },
          },
        },
        {
          condition: 'label',
          attributes: {
            uifieldformatter: { required: false },
          },
        },
        {
          condition: 'textarea',
          attributes: {
            rows: {
              type: 'number',
              required: false,
            },
          },
        },
        {
          condition: 'textareabrief',
        },
        {
          condition: 'text',
          attributes: {
            format: {
              required: false,
            },
            uifieldformatter: { required: false },
            initialize: validate.init({
              ...commonInit,
              alledit: {
                type: 'boolean',
                required: false,
              },
              ispartial: {
                type: 'boolean',
                required: false,
              },
              minlength: {
                type: 'number',
                required: false,
              },
              maxlength: {
                type: 'number',
                required: false,
              },
            }),
          },
        },
        {
          condition: 'dsptextfield',
          attributes: {
            initialize: validate.init({
              ...commonInit,
              transparent: {
                type: 'boolean',
                required: false,
              },
            }),
          },
        },
        {
          condition: 'formattedtext',
          attributes: {
            formatter: {
              required: false,
            },
            uifieldformatter: {
              required: false,
            },
            initialize: validate.init({
              ...commonInit,
              series: {
                type: 'boolean',
                required: false,
              },
              ispartial: {
                type: 'boolean',
                required: false,
              },
            }),
          },
        },
        {
          condition: 'spinner',
          attributes: {
            initialize: validate.init({
              ...commonInit,
              min: {
                type: 'number',
                required: false,
              },
              max: {
                type: 'number',
                required: false,
              },
              step: {
                type: 'number',
                required: false,
              },
            }),
          },
        },
        {
          condition: 'combobox',
          attributes: {
            initialize: pluginInit({
              data: {
                required: false,
              },
            }),
            picklist: { required: false },
          },
        },
        {
          condition: 'plugin',
          attributes: {
            initialize: pluginInit({}),
          },
          switchMapper: (node) =>
            node
              .getAttribute('initialize')
              .split(';')
              .map((value) => value.split('='))
              .find(([name]) => name === 'name')[1],
          switch: [
            {
              condition: 'LatLonUI',
              attributes: {
                initialize: pluginInit({
                  step: {
                    type: 'number',
                    required: false,
                  },
                  latLognType: {
                    required: false,
                    validate: validate.list(['Point', 'Line', 'Rectangle']),
                  },
                }),
              },
            },
            {
              condition: 'PartialDateUI',
              attributes: {
                uifieldformatter: {
                  type: 'string',
                  required: false,
                },
                initialize: pluginInit({
                  df: {},
                  tp: {},
                  defaultPrecision: {
                    required: false,
                    validate: validate.list(['year', 'month-year', 'full']),
                  },
                  canChangePrecision: {
                    type: 'boolean',
                    required: false,
                  },
                }),
              },
            },
            {
              condition: 'CollectionRelOneToManyPlugin',
              attributes: {
                initialize: pluginInit({
                  relName: {},
                  formatting: { required: false },
                }),
              },
            },
            {
              condition: 'ColRelTypePlugin',
              attributes: {
                initialize: pluginInit({
                  relName: {},
                  formatting: { required: false },
                }),
              },
            },
            {
              condition: 'LocalityGeoRef',
              attributes: {
                initialize: pluginInit({
                  geoid: {},
                  locid: {},
                  llid: { type: 'number' },
                }),
              },
            },
            {
              condition: 'WebLinkButton',
              attributes: {
                initialize: pluginInit({
                  weblink: {},
                  icon: { required: false },
                }),
              },
            },
          ],
        },
      ],
    },
  ],
};

export const definitions = {
  viewset: {
    attributes: {
      name: {},
      i18nresname: { required: false },
      'xmlns:xsi': {},
    },
    children: {
      views: {
        children: {
          view: {
            attributes: {
              name: {},
              objtitle: { required: false },
              class: {
                validate: validate.class,
              },
              busrules: {
                required: false,
                validate(value, warn) {
                  const start = 'edu.ku.brc.specify.datamodel.busrules.';
                  if (!value.startsWith(value)) warn('Unknown class name');
                  else {
                    const table = value.slice(start.length);
                    if (!table.endsWith('BusRules')) warn('Incorrect format');
                    else {
                      const parsedTable = table.slice(0, -8);
                      if (!tables.includes(parsedTable))
                        warn('Unknown table name');
                    }
                  }
                },
              },
              isinternal: {
                type: 'boolean',
                required: false,
              },
              isexternal: {
                type: 'boolean',
                required: false,
              },
              usedefbusrule: {
                type: 'boolean',
                required: false,
              },
              resourcelabels: {
                type: 'boolean',
                required: false,
              },
            },
            children: {
              desc: {},
              altviews: {
                attributes: {
                  selector: { required: false },
                  defaultmode: {
                    type: 'string',
                    required: false,
                    validate: validate.list(['view', 'edit', 'search']),
                  },
                },
                children: {
                  altview: {
                    attributes: {
                      name: {},
                      title: { required: false },
                      label: { required: false },
                      validated: { type: 'boolean', required: false },
                      viewdef: {},
                      // Conditional rendering
                      selector_value: { required: false },
                      mode: {
                        validate: validate.list(['view', 'edit', 'search']),
                      },
                      default: {
                        type: 'boolean',
                        required: false,
                      },
                    },
                  },
                },
              },
            },
          },
        },
      },
      viewdefs: {
        children: {
          viewdef: {
            attributes: {
              type: {
                validate: validate.list([
                  'form',
                  'formtable',
                  'iconview',
                  'rstable',
                ]),
              },
              name: {},
              editableDlg: { type: 'boolean', required: false },
              class: {
                validate: validate.class,
              },
              gettable: {},
              settable: {},
              useresourcelabels: {
                type: 'boolean',
                required: false,
              },
            },
            switch: [
              {
                condition: (node) =>
                  node.querySelector(':scope>definition') !== null,
                children: {
                  definition: {},
                  desc: { required: false },
                },
              },
              {
                condition: () => true,
                children: {
                  desc: { required: false },
                  enableRules: {
                    required: false,
                    children: {
                      rule: {
                        required: false,
                        attributes: {
                          id: {},
                        },
                      },
                    },
                  },
                  columnDef: {
                    required: false,
                    attributes: {
                      os: {
                        required: false,
                      },
                    },
                  },
                  rowDef: {
                    required: false,
                    attributes: {
                      auto: {
                        type: 'boolean',
                        required: false,
                      },
                      cell: { required: false },
                      sep: { required: false },
                    },
                  },
                  rows: {
                    required: false,
                    children: {
                      row: {
                        children: {
                          cell: () => cell,
                        },
                      },
                    },
                  },
                },
              },
            ],
          },
        },
      },
    },
  },
};
