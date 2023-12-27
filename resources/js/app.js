import './bootstrap';
import { createApp } from 'vue';
import FlightsDashboard from './components/FlightsDashboard.vue';

const app = createApp({});
app.component('FlightsDashboard', FlightsDashboard);
app.mount("#app");
