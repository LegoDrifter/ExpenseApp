import './bootstrap';
import router from './router.js'
import store from "./Store/index.js"
import {createApp} from "vue";
import '../css/app.css'
import '@mdi/font/css/materialdesignicons.css'

import App from "./App.vue";


import 'vuetify/styles'
import { createVuetify } from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'

const app = createApp(App);

const vuetify = createVuetify({
    components,
    directives,
    iconfont:'mdi',
    theme: {
        defaultTheme: 'light',
        //
    },
})


app.use(router);
app.use(vuetify);
app.use(store);
app.mount("#app");
