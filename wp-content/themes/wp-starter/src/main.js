// https://vitejs.dev/config/#build-polyfillmodulepreload
import 'vite/modulepreload-polyfill';

// Alpine Docs - https://alpinejs.dev/start-here
import Alpine from 'alpinejs';
import playvideo from './components/playvideo.js';
import focus from '@alpinejs/focus';
import persist from '@alpinejs/persist';
window.Alpine = Alpine;

Alpine.data('playvideo', playvideo);

Alpine.plugin(persist);
Alpine.plugin(focus);
Alpine.start();

// Import styles
import './styles/main.css';
