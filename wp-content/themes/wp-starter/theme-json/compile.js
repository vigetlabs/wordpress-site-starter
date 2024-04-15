import settings from "./settings/_index.js";
import styles from "./styles.js";
import config from "./config.js";
import * as fs from 'fs';

const theme = {
  ...config,
  settings: settings,
  styles: styles
}


const json = JSON.stringify(theme)


fs.writeFileSync('theme.json', json)
