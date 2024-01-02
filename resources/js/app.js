import './bootstrap';
import $ from 'jquery';
import { createApp } from 'vue';
import FlightsDashboard from './components/FlightsDashboard.vue';

window.$ = $

const app = createApp({});
app.component('FlightsDashboard', FlightsDashboard);
app.mount("#app");
