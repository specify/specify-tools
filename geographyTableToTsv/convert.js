const fs = require('fs');

const sourceFile = '/Users/maxpatiiuk/Downloads/Result_17.tsv';
const resultFile = '/Users/maxpatiiuk/Downloads/Geography.tsv';

const lines = fs.readFileSync(sourceFile).toString('utf-8').trim().split('\n');

const columns = ['id', 'name', 'parent'];
const ranks = ['Continent', 'Country', 'State', 'County'];

const nodes = Object.fromEntries(
  lines
    .map((line) =>
      Object.fromEntries(
        line.split('\t').map((value, index) => [columns[index], value])
      )
    )
    .map(({ id, name, parent }) => [
      id,
      {
        name,
        parent,
        children: [],
      },
    ])
);

Object.entries(nodes)
  .reverse()
  .forEach(([id, node]) => {
    if (node.parent in nodes) {
      nodes[node.parent].children.push({
        name: node.name,
        children: nodes[id].children,
      });
      nodes[id] = undefined;
    }
  });

const tree = Object.entries(nodes).filter(([_id, value]) => value)[0][1]
  .children;
let output = [];

function drawTree(node, parents = []) {
  const nodeString = [...parents, node.name];
  output.push(nodeString);
  node.children.forEach((node) => drawTree(node, nodeString));
}

tree.forEach((node) => drawTree(node));

const result = [ranks, ...output]
  .map((line) => line.join('\t'))
  .join('\n');

fs.writeFileSync(resultFile, result);
