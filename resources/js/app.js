import './bootstrap';
import { createApp } from 'vue';
import GlobalFlights from './components/Global.vue';

const app = createApp({});
app.component('GlobalFlights', GlobalFlights);
app.mount("#app");
