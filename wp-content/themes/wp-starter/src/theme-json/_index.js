import settings from "./settings/_index.js";
import styles from "./styles/_index.js";
import * as fs from 'fs';

const json = {
	"$schema": "https://schemas.wp.org/trunk/theme.json",
	"version": 2,
	settings: settings,
	styles: styles
}

fs.writeFileSync('theme.json', JSON.stringify(json))
