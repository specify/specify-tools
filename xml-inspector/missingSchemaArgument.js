import { formatUrl } from './queryString.js';

export function run() {
  document.body.innerHTML = `
  Please, specify which schema you wish to use.<br/>
  See ./schema directory for available options
`;

  const form = document.createElement('form');
  form.addEventListener('submit', (event) => {
    event.preventDefault();
    const schema = input.value;
    window.location.assign(formatUrl(undefined, { schema }));
  });

  const input = document.createElement('input');
  input.type = 'text';
  input.required = true;

  const submit = document.createElement('input');
  submit.type = 'submit';

  form.append(input);
  form.append(submit);
  document.body.append(form);
}
