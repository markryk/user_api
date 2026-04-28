//import "./assets/main.css";
//import Vue from 'vue'
import { /*Vue,*/ createApp } from 'vue'
import { createPinia } from 'pinia'
//import { BootstrapVue } from 'bootstrap-vue';
//import 'bootstrap/dist/css/bootstrap.css';
//import 'bootstrap-vue/dist/bootstrap-vue.css';

// Vuetify
/*import 'vuetify/styles'
import { createVuetify } from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'*/

import router from './router'
import App from './App.vue'
import './assets/tailwind.css'

//window.Vue = Vue;

/*const vuetify = createVuetify({
  components,
  directives,
})*/

//createApp(App).use(vuetify).mount('#app')

const app = createApp(App)
app.use(createPinia())
//app.use(vuetify)
app.use(router)
app.mount('#app')

// Importe e adicione o BootstrapVue ao Vue
//Vue.use(BootstrapVue);