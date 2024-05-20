// https://vitejs.dev/config/#build-polyfillmodulepreload
import 'vite/modulepreload-polyfill';

// Alpine Docs - https://alpinejs.dev/start-here
import Alpine from 'alpinejs';
import persist from '@alpinejs/persist'
window.Alpine = Alpine;

Alpine.plugin(persist);
Alpine.start();

import './script/dropdown.js';

// Import styles
import './styles/main.css';
