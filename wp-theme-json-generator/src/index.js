import * as fs from 'fs';
import * as path from 'path';
import getSettings from './settings/_index.js';
import getStyles from './styles/_index.js';
import getTemplatePartsSettings from './template-parts/_index.js';

/**
 * WordPress Theme JSON Generator
 * Extracts CSS variables and generates WordPress theme.json configuration
 */
class WPThemeJSONGenerator {
  constructor(options = {}) {
    this.options = {
      cssPath: options.cssPath || 'src/styles/tailwind.css',
      outputPath: options.outputPath || 'theme.json',
      templatePartsPath: options.templatePartsPath || 'parts',
      ...options
    };
  }

  /**
   * Generate the theme.json file
   * @param {Object} customSettings - Optional custom settings to merge
   * @param {Object} customStyles - Optional custom styles to merge
   * @param {Array} customTemplateParts - Optional custom template parts to merge
   * @returns {Object} The generated theme.json data
   */
  generate(customSettings = {}, customStyles = {}, customTemplateParts = []) {
    const settings = getSettings(this.options.cssPath);
    const styles = getStyles(this.options.cssPath);
    const templateParts = getTemplatePartsSettings(this.options.templatePartsPath);
    
    const data = {
      settings: { ...settings, ...customSettings },
      styles: { ...styles, ...customStyles },
      templateParts: [...templateParts, ...customTemplateParts],
      version: 3,
      $schema: 'https://schemas.wp.org/wp/6.8/theme.json',
    };

    return data;
  }

  /**
   * Generate and write the theme.json file to disk
   * @param {Object} customSettings - Optional custom settings to merge
   * @param {Object} customStyles - Optional custom styles to merge
   * @param {Array} customTemplateParts - Optional custom template parts to merge
   * @returns {string} The path to the generated file
   */
  generateFile(customSettings = {}, customStyles = {}, customTemplateParts = []) {
    const data = this.generate(customSettings, customStyles, customTemplateParts);
    const outputPath = path.resolve(this.options.outputPath);
    
    // Ensure the directory exists
    const outputDir = path.dirname(outputPath);
    if (!fs.existsSync(outputDir)) {
      fs.mkdirSync(outputDir, { recursive: true });
    }

    fs.writeFileSync(outputPath, JSON.stringify(data, null, 2));
    return outputPath;
  }

  /**
   * Watch for changes and regenerate the theme.json file
   * @param {Function} callback - Optional callback function to run after generation
   */
  watch(callback = null) {
    const watchPaths = [
      path.resolve(this.options.cssPath),
      path.resolve('src/theme-json'),
      path.resolve(this.options.templatePartsPath)
    ];

    watchPaths.forEach(watchPath => {
      if (fs.existsSync(watchPath)) {
        fs.watch(watchPath, { recursive: true }, () => {
          console.log(`Change detected in ${watchPath}, regenerating theme.json...`);
          this.generateFile();
          if (callback) callback();
        });
      }
    });

    console.log('Watching for changes...');
  }
}

// Export the class
export default WPThemeJSONGenerator;

// Also export a simple function for backward compatibility
export function generateThemeJSON(options = {}) {
  const generator = new WPThemeJSONGenerator(options);
  return generator.generate();
}

export function generateThemeJSONFile(options = {}) {
  const generator = new WPThemeJSONGenerator(options);
  return generator.generateFile();
}

// Export individual components for advanced usage
export { getSettings as settings, getStyles as styles, getTemplatePartsSettings as templateParts }; 
