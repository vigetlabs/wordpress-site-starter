import fs from 'fs';
import path from 'path';

/**
 * Parse the CSS file to extract spacing variables from the @theme directive.
 *
 * @returns {object} The spacing variables object
 */
function parseSpacingFromCSS() {
	const cssPath = path.join(process.cwd(), 'src/styles/tailwind/spacing.css');

	try {
		const cssContent = fs.readFileSync(cssPath, 'utf8');

		// Find the @theme block
		const themeMatch = cssContent.match(/@theme\s*\{([\s\S]*?)\}/);
		if (!themeMatch) {
			console.warn('No @theme directive found in CSS file');
			return {};
		}

		const themeContent = themeMatch[1];

		// Extract all spacing variables (--spacing-*)
		// Handle multi-line values like clamp() expressions by matching
		// everything between the colon and semicolon, accounting for nested parentheses
		const spacing = {};
		const lines = themeContent.split('\n');
		let currentVar = null;
		let currentValue = [];
		let parenDepth = 0;

		for (let i = 0; i < lines.length; i++) {
			const line = lines[i].trim();

			// Skip empty lines and comments
			if (!line || line.startsWith('/*') || line.startsWith('*')) {
				continue;
			}

			// Check if this line starts a new variable
			const varMatch = line.match(/^--spacing-([^:]+):\s*(.*)$/);
			if (varMatch) {
				// Save previous variable if exists
				if (currentVar) {
					const value = currentValue.join(' ').replace(/\s+/g, ' ').replace(/;.*$/, '').trim();
					spacing[currentVar] = value;
				}
				// Start new variable
				currentVar = varMatch[1].trim();
				const valuePart = varMatch[2].trim();
				currentValue = valuePart ? [valuePart] : [];
				// Count parentheses in the value part
				parenDepth = (valuePart.match(/\(/g) || []).length - (valuePart.match(/\)/g) || []).length;

				// If the value ends with a semicolon and we're not in nested parentheses, we're done
				if (valuePart.includes(';') && parenDepth <= 0) {
					const value = valuePart.replace(/;.*$/, '').trim();
					spacing[currentVar] = value;
					currentVar = null;
					currentValue = [];
					parenDepth = 0;
				}
			} else if (currentVar && line) {
				// Continue collecting value for current variable
				currentValue.push(line);
				// Update parenthesis depth
				parenDepth += (line.match(/\(/g) || []).length - (line.match(/\)/g) || []).length;
				// If we hit a semicolon and parenDepth is 0, we're done with this variable
				if (line.includes(';') && parenDepth <= 0) {
					// Remove semicolon from the last part
					const lastPart = currentValue[currentValue.length - 1];
					if (lastPart) {
						currentValue[currentValue.length - 1] = lastPart.replace(/;.*$/, '');
					}
					const value = currentValue.join(' ').replace(/\s+/g, ' ').trim();
					spacing[currentVar] = value;
					currentVar = null;
					currentValue = [];
					parenDepth = 0;
				}
			}
		}

		// Save last variable if exists (remove any trailing semicolon)
		if (currentVar) {
			const value = currentValue.join(' ').replace(/\s+/g, ' ').replace(/;.*$/, '').trim();
			spacing[currentVar] = value;
		}

		return spacing;
	} catch (error) {
		console.error('Error reading CSS file:', error);
		return {};
	}
}

/**
 * Resolve nested var() references in a spacing value.
 * For example, var(--spacing-6) becomes 6px
 *
 * @param {string} value - The spacing value that may contain var() references
 * @param {object} spacingVars - All spacing variables from CSS
 * @returns {string} The resolved value
 */
function resolveSpacingVars(value, spacingVars) {
	// Match var(--spacing-XXX) patterns
	const varRegex = /var\(--spacing-([^)]+)\)/g;

	return value.replace(varRegex, (match, varName) => {
		// Look up the variable value
		if (spacingVars[varName]) {
			const varValue = spacingVars[varName];
			// If the value itself contains var(), recursively resolve it
			if (varValue.includes('var(')) {
				return resolveSpacingVars(varValue, spacingVars);
			}
			return varValue;
		}
		// If variable not found, return the original match
		return match;
	});
}

/**
 * Extract the minimum value from a clamp() expression for sorting purposes.
 * Returns the numeric pixel value of the first argument in clamp(min, preferred, max).
 *
 * @param {string} clampValue - The clamp() expression
 * @param {object} spacingVars - All spacing variables for resolving var() references
 * @returns {number} The minimum pixel value, or 0 if unable to parse
 */
function getMinValueFromClamp(clampValue, spacingVars) {
	// Match clamp(min, preferred, max) pattern
	const clampMatch = clampValue.match(/clamp\s*\(\s*([^,]+),/);
	if (!clampMatch) {
		return 0;
	}

	// Get the first argument (minimum value)
	const minValue = clampMatch[1].trim();

	// Resolve any var() references
	const resolvedMin = resolveSpacingVars(minValue, spacingVars);

	// Extract numeric value (assumes px units)
	const pxMatch = resolvedMin.match(/(\d+(?:\.\d+)?)\s*px/i);
	if (pxMatch) {
		return parseFloat(pxMatch[1]);
	}

	// If no px value found, try to parse as number (might be just a number)
	const numMatch = resolvedMin.match(/(\d+(?:\.\d+)?)/);
	if (numMatch) {
		return parseFloat(numMatch[1]);
	}

	return 0;
}

/**
 * Get the spacing sizes array for theme.json.
 * Dynamically finds all "fluid" spacing variables, sorts them by size (smallest to largest),
 * and assigns names 1-9 with slugs 10-90 (limited to 9 items max).
 *
 * @returns {array} Array of spacing size objects
 */
function getSpacingSizes() {
	const spacingVars = parseSpacingFromCSS();
	const spacingSizes = [];

	// Filter to only include variables with "fluid" in the name
	const fluidVars = Object.entries(spacingVars)
		.filter(([varName]) => varName.includes('fluid'))
		.map(([varName, value]) => ({
			varName,
			value,
			// Calculate min value for sorting
			minValue: getMinValueFromClamp(value, spacingVars),
		}))
		// Sort by minimum value (smallest to largest)
		.sort((a, b) => a.minValue - b.minValue)
		// Limit to 9 items
		.slice(0, 9);

	// Assign sequential names (1-9) and slugs (10-90)
	for (let i = 0; i < fluidVars.length; i++) {
		const fluidVar = fluidVars[i];
		// Resolve nested var() references in the value
		const resolvedValue = resolveSpacingVars(fluidVar.value, spacingVars);

		spacingSizes.push({
			name: String(i + 1),
			size: resolvedValue,
			slug: String((i + 1) * 10),
		});
	}

	return spacingSizes;
}

export { getSpacingSizes };
