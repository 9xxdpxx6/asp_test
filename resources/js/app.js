// Подключение настроек из bootstrap.js
import './bootstrap'

// Подключение SCSS
import '../sass/app.scss'

// Подключение Vue.js
import { createApp } from 'vue'
import App from './App.vue'
import router from './router'

// Подключение Bootstrap JavaScript
import 'bootstrap/dist/js/bootstrap.bundle.min.js'

// Создание приложения Vue
createApp(App)
    .use(router)
    .mount('#app')
