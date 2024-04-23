// https://vitejs.dev/config/#build-polyfillmodulepreload
import 'vite/modulepreload-polyfill'


// Alpine Docs - https://alpinejs.dev/start-here
import Alpine from 'alpinejs'
window.Alpine = Alpine
Alpine.start()

// Import styles
import './styles/main.css'

