# WP Theme JSON Generator

A WordPress theme.json generator that extracts CSS variables from your stylesheets and automatically creates WordPress theme configuration files.

## Features

- 🎨 **CSS Variable Extraction**: Automatically extracts color and gradient variables from CSS files
- 📝 **Template Parts Detection**: Scans your theme parts directory and generates template part configurations
- ⚙️ **Configurable Paths**: Customize CSS file paths, template parts directories, and output locations
- 🔄 **Watch Mode**: Automatically regenerate theme.json when files change
- 🎯 **WordPress Standards**: Generates valid WordPress theme.json files with proper schema

## Installation

```bash
npm install wp-theme-json-generator
```

## Quick Start

### Basic Usage

```javascript
import WPThemeJSONGenerator from "wp-theme-json-generator";

// Create a generator instance
const generator = new WPThemeJSONGenerator({
  cssPath: "src/styles/tailwind.css",
  outputPath: "theme.json",
  templatePartsPath: "parts",
});

// Generate theme.json data
const themeData = generator.generate();

// Generate and save to file
const outputPath = generator.generateFile();
console.log(`Theme.json generated at: ${outputPath}`);
```

### Simple Function Usage

```javascript
import {
  generateThemeJSON,
  generateThemeJSONFile,
} from "wp-theme-json-generator";

// Generate theme.json data
const themeData = generateThemeJSON({
  cssPath: "src/styles/tailwind.css",
});

// Generate and save to file
const outputPath = generateThemeJSONFile({
  cssPath: "src/styles/tailwind.css",
  outputPath: "theme.json",
});
```

### Watch Mode

```javascript
import WPThemeJSONGenerator from "wp-theme-json-generator";

const generator = new WPThemeJSONGenerator({
  cssPath: "src/styles/tailwind.css",
  outputPath: "theme.json",
});

// Watch for changes and regenerate automatically
generator.watch(() => {
  console.log("Theme.json updated!");
});
```

## Configuration Options

### Generator Options

| Option              | Type   | Default                     | Description                                         |
| ------------------- | ------ | --------------------------- | --------------------------------------------------- |
| `cssPath`           | string | `'src/styles/tailwind.css'` | Path to your CSS file containing `@theme` directive |
| `outputPath`        | string | `'theme.json'`              | Output path for the generated theme.json file       |
| `templatePartsPath` | string | `'parts'`                   | Path to your template parts directory               |

### CSS File Format

Your CSS file should contain a `@theme` directive with color and gradient variables:

```css
@theme {
  --color-primary: #007cba;
  --color-secondary: #005a87;
  --color-accent: #00a0d2;
  --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  --gradient-secondary: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}
```

## Advanced Usage

### Custom Settings and Styles

```javascript
import WPThemeJSONGenerator from "wp-theme-json-generator";

const generator = new WPThemeJSONGenerator();

// Generate with custom settings
const themeData = generator.generate(
  {
    // Custom settings
    custom: {
      setting: "value",
    },
  },
  {
    // Custom styles
    custom: {
      style: "value",
    },
  },
  [
    // Custom template parts
    {
      name: "custom-part",
      title: "Custom Part",
      area: "header",
    },
  ]
);
```

### Using Individual Components

```javascript
import { settings, styles, templateParts } from "wp-theme-json-generator";

// Get settings with custom CSS path
const themeSettings = settings("path/to/your/css/file.css");

// Get template parts with custom path
const parts = templateParts("path/to/your/parts/directory");

// Use styles directly
const themeStyles = styles;
```

## Vite Plugin Integration

This package can be used as a Vite plugin:

```javascript
// vite.config.js
import { defineConfig } from "vite";
import WPThemeJSONGenerator from "wp-theme-json-generator";

export default defineConfig({
  plugins: [
    WPThemeJSONGenerator({
      cssPath: "src/styles/tailwind.css",
      outputPath: "theme.json",
    }),
  ],
});
```

## API Reference

### WPThemeJSONGenerator Class

#### Constructor

```javascript
new WPThemeJSONGenerator(options);
```

#### Methods

##### `generate(customSettings, customStyles, customTemplateParts)`

Generates theme.json data without writing to file.

**Parameters:**

- `customSettings` (Object): Custom settings to merge
- `customStyles` (Object): Custom styles to merge
- `customTemplateParts` (Array): Custom template parts to merge

**Returns:** Object - The generated theme.json data

##### `generateFile(customSettings, customStyles, customTemplateParts)`

Generates theme.json data and writes to file.

**Parameters:** Same as `generate()`

**Returns:** string - Path to the generated file

##### `watch(callback)`

Watches for file changes and regenerates theme.json automatically.

**Parameters:**

- `callback` (Function): Optional callback to run after generation

### Utility Functions

#### `generateThemeJSON(options)`

Simple function to generate theme.json data.

#### `generateThemeJSONFile(options)`

Simple function to generate and save theme.json file.

## Examples

### Basic WordPress Theme Integration

```javascript
// build-theme.js
import { generateThemeJSONFile } from "wp-theme-json-generator";

// Generate theme.json for your WordPress theme
generateThemeJSONFile({
  cssPath: "src/styles/tailwind.css",
  outputPath: "wp-content/themes/your-theme/theme.json",
  templatePartsPath: "wp-content/themes/your-theme/parts",
});
```

### Package.json Script

```json
{
  "scripts": {
    "build:theme": "node build-theme.js",
    "watch:theme": "node -e \"require('wp-theme-json-generator').default().watch()\""
  }
}
```

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Support

If you encounter any issues or have questions, please [open an issue](https://github.com/yourusername/wp-theme-json-generator/issues) on GitHub.
