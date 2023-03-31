import { definitions } from "./definitions.js";
import { execute, formatWarns } from "./execute.js";

execute()
  .then(() => {
    console.log(definitions);
    console.warn(formatWarns());
  })
  .catch(console.error);
