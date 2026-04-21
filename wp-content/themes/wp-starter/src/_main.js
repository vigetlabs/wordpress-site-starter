// Load dependencies
import 'vite/modulepreload-polyfill';

// Import AlpineJS and plugins
import Alpine from 'alpinejs';
import focus from '@alpinejs/focus';
import persist from '@alpinejs/persist';

// Import block scripts
import playvideo from '../blocks/video-player/script.js';

// Initialize Alpine
window.Alpine = Alpine;

// Initialize custom scripts
Alpine.data('playvideo', playvideo);

// Initialize Alpine plugins
Alpine.plugin(persist);
Alpine.plugin(focus);

// Start Alpine
Alpine.start();
