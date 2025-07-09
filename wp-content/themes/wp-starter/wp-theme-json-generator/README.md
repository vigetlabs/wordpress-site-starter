# WP Starter Theme JSON Generator

A WordPress theme.json generator that extracts CSS variables from your stylesheets and automatically creates WordPress theme configuration files. This package is integrated into the wp-starter theme.

## Features

- 🎨 **CSS Variable Extraction**: Automatically extracts color and gradient variables from CSS files
- 📝 **Template Parts Detection**: Scans your theme parts directory and generates template part configurations
- ⚙️ **Configurable Paths**: Customize CSS file paths, template parts directories, and output locations
- 🔄 **Watch Mode**: Automatically regenerate theme.json when files change
- 🎯 **WordPress Standards**: Generates valid WordPress theme.json files with proper schema
- 🏗️ **Vite Integration**: Works seamlessly with your existing Vite build process

## Usage

### Automatic Generation

The theme.json file is automatically generated when you run:

```bash
npm run build
```

This will:

1. Build your theme assets with Vite
2. Generate the theme.json file from your CSS variables

### Manual Generation

You can also generate the theme.json file manually:

```bash
npm run build:theme-json
```

### Development Mode

When running `npm run dev`, the theme.json file will be automatically regenerated whenever you make changes to your CSS files.

## Configuration

The generator is configured in your `vite.config.js` file:

```javascript
import WPThemeJSONGenerator from './wp-theme-json-generator/src/index.js';

// In your Vite config
{
  name: 'generate-theme-json',
  configureServer(server) {
    const generator = new WPThemeJSONGenerator({
      cssPath: 'src/styles/tailwind.css',
      outputPath: 'theme.json'
    });
    generator.watch();
  },
  closeBundle() {
    const generator = new WPThemeJSONGenerator({
      cssPath: 'src/styles/tailwind.css',
      outputPath: 'theme.json'
    });
    generator.generateFile();
  }
}
```

## CSS File Format

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

You can customize the generated theme.json by modifying the files in:

- `src/settings/` - Theme settings (colors, typography, spacing, etc.)
- `src/styles/` - Theme styles (block styles, elements, etc.)
- `src/template-parts/` - Template part configurations

### Using the Generator Programmatically

```javascript
import WPThemeJSONGenerator from './wp-theme-json-generator/src/index.js';

const generator = new WPThemeJSONGenerator({
	cssPath: 'src/styles/tailwind.css',
	outputPath: 'theme.json',
	templatePartsPath: 'parts',
});

// Generate theme.json data
const themeData = generator.generate();

// Generate and save to file
const outputPath = generator.generateFile();

// Watch for changes
generator.watch(() => {
	console.log('Theme.json updated!');
});
```

## File Structure

```
wp-theme-json-generator/
├── src/
│   ├── index.js              # Main entry point
│   ├── settings/             # Theme settings
│   │   ├── _index.js
│   │   ├── color.js          # Color palette generation
│   │   ├── typography.js     # Typography settings
│   │   ├── spacing.js        # Spacing settings
│   │   ├── layout.js         # Layout settings
│   │   └── blocks.js         # Block settings
│   ├── styles/               # Theme styles
│   │   ├── _index.js
│   │   ├── blocks.js         # Block styles
│   │   ├── elements.js       # Element styles
│   │   └── css.js            # Custom CSS generation
│   └── template-parts/       # Template parts
│       └── _index.js
├── dist/                     # Built files
├── example.js                # Usage example
├── package.json
└── README.md
```

## Integration with wp-starter Theme

This package is specifically designed to work with the wp-starter theme:

- **CSS Path**: Points to `src/styles/tailwind.css` where your `@theme` directive is located
- **Output Path**: Generates `theme.json` in the theme root
- **Template Parts**: Scans the `parts/` directory for template parts
- **Build Integration**: Automatically runs during `npm run build`

## Development

To modify the theme.json generation logic:

1. Edit the files in `src/settings/`, `src/styles/`, or `src/template-parts/`
2. Run `npm run build` in the `wp-theme-json-generator` directory to rebuild
3. Test with `node example.js`

## License

This project is licensed under the MIT License.
