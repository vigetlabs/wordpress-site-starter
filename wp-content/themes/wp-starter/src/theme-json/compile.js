import settings from "./settings/_index.js";
import styles from "./styles.js";
import config from "./config.js";
import * as fs from 'fs';

const json = {
  ...config,
  settings: settings,
  styles: styles
}

fs.writeFileSync('theme.json', JSON.stringify(json))
