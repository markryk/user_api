import { createApp } from 'vue'
import { createPinia } from 'pinia'
import router from './router'
import App from './App.vue'
import './assets/tailwind.css'

const app = createApp(App)
app.use(createPinia())
app.use(router)
app.mount('#app') //"#app" chama o bloco de id == app (arquivo App.vue)