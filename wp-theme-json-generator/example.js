#!/usr/bin/env node

/**
 * Example usage of WP Theme JSON Generator
 * 
 * This file demonstrates how to use the package to generate
 * WordPress theme.json files from CSS variables.
 */

import WPThemeJSONGenerator from './src/index.js';

// Example 1: Basic usage
console.log('=== Example 1: Basic Usage ===');

const generator = new WPThemeJSONGenerator({
  cssPath: 'example-styles.css',
  outputPath: 'example-theme.json',
  templatePartsPath: 'example-parts'
});

// Generate theme.json data
const themeData = generator.generate();
console.log('Generated theme data:', JSON.stringify(themeData, null, 2));

// Example 2: Generate file
console.log('\n=== Example 2: Generate File ===');

try {
  const outputPath = generator.generateFile();
  console.log(`Theme.json generated at: ${outputPath}`);
} catch (error) {
  console.log('Note: Could not generate file (CSS file may not exist)');
  console.log('Error:', error.message);
}

// Example 3: With custom settings
console.log('\n=== Example 3: Custom Settings ===');

const customThemeData = generator.generate(
  {
    // Custom settings
    custom: {
      setting: 'value'
    }
  },
  {
    // Custom styles
    custom: {
      style: 'value'
    }
  },
  [
    // Custom template parts
    {
      name: 'custom-part',
      title: 'Custom Part',
      area: 'header'
    }
  ]
);

console.log('Custom theme data:', JSON.stringify(customThemeData, null, 2));

console.log('\n=== Example Complete ===');
console.log('Check the generated files and see the README.md for more examples!'); 
