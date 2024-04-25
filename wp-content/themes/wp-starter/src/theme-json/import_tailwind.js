import resolveConfig from 'tailwindcss/resolveConfig.js';
import tailwindConfig from '../../tailwind.config.js';
const { theme } = resolveConfig(tailwindConfig);

// Export theme from tailwind
export default {
	...theme,
};
