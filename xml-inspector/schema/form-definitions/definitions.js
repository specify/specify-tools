/**
 * Form schema and validation rules are defined here
 */

import { tables } from '../tables.js';
import { validateAttributes } from '../../utils.js';

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
  initialize(value, warn, node) {
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
      'property',
      node
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
  // Sp6 looks at this prop for some cells only
  visible: {
    type: 'boolean',
    required: false,
    default: true,
  },
};

const border = {
  border: {
    required: false,
    validate: validate.list(['etched', 'lowered', 'raised', 'empty', 'line']),
  },
  bordercolor: { required: false },
  bgcolor: { required: false },
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
    colspan: {
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
    desc: {
      required: false,
    },
  },
  switch: [
    {
      condition: 'label',
      attributes: {
        labelfor: {
          required: false,
        },
        /*
         * Sp6's behavior:
         * right align the label if it ends with ':'
         * append ":" automatically if align right
         */
        label: {
          required: false,
        },
        icon: {
          required: false,
        },
        name: { required: false },
      },
    },
    {
      condition: 'iconview',
      attributes: {
        name: {},
        viewsetname: {
          required: false,
        },
        viewname: {},
        desc: {},
        initialize: validate.init({
          nosep: {
            type: 'boolean',
            required: false,
            default: false,
          },
          nosepmorebtn: {
            type: 'boolean',
            required: false,
            default: false,
          },
        }),
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
        collapse: {
          type: 'boolean',
          required: false,
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
          default: 5,
        },
        viewsetname: {
          required: false,
        },
        viewname: {
          required: false,
        },
        valtype: {
          required: false,
          validate: validate.list(['Changed', 'Focus', 'None', 'OK']),
        },
        readonly: {
          type: 'boolean',
          required: false,
        },
        initialize: validate.init({
          ...commonInit,
          ...border,
          btn: {
            type: 'boolean',
            required: false,
            default: false,
          },
          icon: {
            required: false,
          },
          // Help context
          hc: {
            required: false,
          },
          sortfield: {
            required: false,
          },
          noscrollbars: {
            type: 'boolean',
            required: false,
            default: false,
          },
          nosep: {
            type: 'boolean',
            required: false,
            default: false,
          },
          nosepmorebtn: {
            type: 'boolean',
            required: false,
            default: false,
          },
          many: {
            type: 'boolean',
            required: false,
            default: false,
          },
          collapse: {
            type: 'boolean',
            required: false,
            default: false,
          },
          addsearch: {
            type: 'boolean',
            required: false,
            default: false,
          },
          addadd: {
            type: 'boolean',
            required: false,
            default: false,
          },
        }),
      },
    },
    {
      condition: 'panel',
      attributes: {
        coldef: {
          required: false,
        },
        // Used by sp6 only and only for a reserved hardcoded list of names
        name: {
          required: false,
          validate: validate.list([
            'outerPanel',
            'mgrpanel',
            'srcPanel',
            'sidePanel',
            'innerPanel',
            'ContainerPanel',
            'CPInner',
            'upperPanel',
            'outerPanell',
            'outerPanelx',
          ]),
        },
        rowdef: {
          required: false,
        },
        initialize: validate.init({
          ...commonInit,
          ...border,
        }),
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
        action: {
          required: false,
          validate: validate.list(['ReturnLoan']),
        },
        initialize: validate.init({
          ...commonInit,
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
        initialize: validate.init({
          ...commonInit,
          editoncreate: {
            required: false,
            type: 'boolean',
          },
        }),
        name: {},
        uitype: {
          required: false,
          validate: validate.list([
            'combobox',
            'formattedtext',
            'text',
            'dsptextfield',
            'textfieldinfo',
            'plugin',
            'querycbx',
            'textareabrief',
            'label',
            'checkbox',
            'textarea',
            'spinner',
            'list',
            'image',
            'browse',
          ]),
          default: 'text',
        },
        // Not used in the code
        dsptype: {
          required: false,
        },
        isrequired: {
          type: 'boolean',
          required: false,
        },
        valtype: {
          required: false,
          validate: validate.list(['Changed', 'Focus']),
          default: 'Changed',
        },
        readonly: {
          type: 'boolean',
          required: false,
        },
        default: {
          required: false,
        },
        format: {
          required: false,
        },
        formatname: {
          required: false,
        },
        uifieldformatter: { required: false },
      },
      switchMapper: (node) => node.getAttribute('uitype') ?? 'text',
      switch: [
        {
          condition: 'querycbx',
          attributes: {
            initialize: validate.init({
              ...commonInit,
              // Make query compatible with multiple ORMs
              adjustquery: { type: 'boolean', required: false, default: true },
              // Customize view name
              displaydlg: { required: false },
              searchdlg: { required: false },
              searchbtn: { required: false, default: true },
              clonebtn: { type: 'boolean', required: false, default: false },
              editbtn: { type: 'boolean', required: false, default: true },
              newbtn: { type: 'boolean', required: false, default: true },
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
              rows: { type: 'number', default: 10 },
              data: { required: false },
            }),
          },
        },
        {
          condition: 'tristate',
        },
        {
          condition: 'checkbox',
          attributes: {
            initialize: validate.init({
              ...commonInit,
              editable: {
                type: 'boolean',
                required: false,
              },
              // This is not looked at in the code, but often specified
              vis: {
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
            name: { required: false },
          },
        },
        {
          condition: 'textarea',
          attributes: {
            rows: {
              type: 'number',
              required: false,
              default: 4,
            },
            cols: {
              type: 'number',
              required: false,
              default: 10,
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
            cols: {
              type: 'number',
              required: false,
              default: 10,
            },
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
              ispassword: {
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
                default: false,
              },
            }),
          },
        },
        {
          condition: 'textfieldinfo',
          attributes: {
            initialize: validate.init({
              ...commonInit,
              displaydlg: { type: 'boolean', required: false },
            }),
          },
        },
        {
          condition: 'formattedtext',
          attributes: {
            initialize: validate.init({
              ...commonInit,
              series: {
                type: 'boolean',
                required: false,
                default: false,
              },
              alledit: {
                type: 'boolean',
                required: false,
                default: false,
              },
              ispartial: {
                type: 'boolean',
                required: false,
                default: false,
              },
              fromuifmt: {
                type: 'boolean',
                required: false,
                default: false,
              },
              transparent: {
                type: 'boolean',
                required: false,
              },
            }),
          },
        },
        {
          condition: 'image',
          attributes: {
            initialize: validate.init({
              ...commonInit,
              size: {
                type: 'string',
                default: '150,150',
                validate: (value, warn) => {
                  if (!value.match(/^\d+,\d+$/))
                    warn('size must be in the format "width,height"');
                },
              },
              /*
               * Whether to display the image. By default the image is only
               * displayed when in edit mode
               */
              edit: {
                type: 'boolean',
                required: false,
              },
              border: {
                type: 'boolean',
                required: false,
                default: true,
              },
              url: {
                required: false,
              },
              icon: {
                required: false,
              },
              iconsize: {
                type: 'number',
                required: false,
              },
            }),
          },
        },
        {
          condition: 'url',
          attributes: validate.init({
            ...commonInit,
          }),
        },
        {
          condition: 'colorchooser',
          attributes: {},
        },
        {
          condition: 'browse',
          attributes: {
            initialize: validate.init({
              ...commonInit,
              dirsonly: { type: 'boolean', required: false, default: false },
              forinput: { type: 'boolean', required: false, default: true },
              filefilter: { required: false },
              filefilterdesc: { required: false },
              defaultExtension: { required: false },
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
            initialize: validate.init({
              ...commonInit,
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
                  latLongType: {
                    required: false,
                    validate: validate.list(['Point', 'Line', 'Rectangle']),
                  },
                }),
              },
            },
            {
              condition: 'PartialDateUI',
              attributes: {
                initialize: pluginInit({
                  df: {},
                  tp: {},
                  defaultprecision: {
                    required: false,
                    validate: validate.list(['year', 'month-year', 'full']),
                  },
                  canchangeprecision: {
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
                  relname: {},
                  formatting: { required: false },
                }),
              },
            },
            {
              condition: 'ColRelTypePlugin',
              attributes: {
                initialize: pluginInit({
                  relname: {},
                  formatting: { required: false },
                  // Force consider current to be right side
                  rightside: { type: 'boolean', required: false },
                }),
              },
            },
            {
              condition: 'LocalityGeoRef',
              attributes: {
                initialize: pluginInit({
                  /*
                   * If geography is not set, get the value from a field with
                   * this ID instead
                   */
                  geoid: {},
                  /*
                   * If locality is not set, get the value from a field with
                   * this ID instead
                   */
                  locid: {},
                  /*
                   * ID of the LatLongUI plugin. If set, precision and
                   * latLongMethod will be updated after geo-referencing
                   */
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
                  url: { required: false },
                }),
              },
            },
            {
              condition: 'HostTaxonPlugin',
              attributes: {
                initialize: pluginInit({
                  relname: {},
                }),
              },
            },
            {
              condition: () => true,
              validate(node, warn) {
                warn('Unknown plugin', [node.getAttribute('initialize')]);
              },
            },
          ],
        },
        {
          condition: () => true,
          validate(node, warn) {
            warn('Unknown uitype', [node.getAttribute('uitype')]);
          },
        },
      ],
    },
    {
      condition: () => true,
      validate(node, warn) {
        warn('Unknown cell type', [node.getAttribute('type')]);
      },
    },
  ],
};
console.log('Cell definitions', cell);

export const definitions = {
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
            // Not used in the code, but specified in the XML often
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
              nodes: [],
              modes: {
                edit: new Set(),
                view: new Set(),
                search: new Set(),
              },
              validate(node) {
                if (this.nodes.length === 0) console.warn('Altviews', this);
                const def = Array.from(node.children).find((v) =>
                  v.getAttribute('default')
                );
                if (def !== undefined) {
                  const mode = def.getAttribute('mode');
                  this.modes[mode].add(
                    node.parentElement.getAttribute('class')
                  );
                }
                this.nodes.push([
                  def,
                  Array.from(node.children),
                  // ...Array.from(node.children).map((v) => v.outerHTML),
                ]);
              },
              children: {
                altview: {
                  attributes: {
                    name: {},
                    title: { required: false },
                    // This value is specified several times, but not used in the code
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
          children: {
            desc: { required: false },
          },
          switch: [
            {
              condition: (node) =>
                node.querySelector(':scope>definition') !== null,
              children: {
                definition: {
                  nodes: [],
                  validate(node) {
                    if (this.nodes.length === 0) console.log(this.nodes);
                    this.nodes.push(node);
                  },
                },
              },
            },
            {
              condition: () => true,
              children: {
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
};
