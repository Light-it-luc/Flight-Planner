import './bootstrap';
import { createApp } from 'vue';
import FlightFilters from './components/FlightFliters.vue';

const app = createApp({});
app.component('FlightFilters', FlightFilters);
app.mount("#app");
