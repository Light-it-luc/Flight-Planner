export default (await import('vue')).defineComponent({
data() {
return {
selected: 0
};
},

props: {
cities: Array,
disabledCity: Number
},

methods: {},

computed: {
availableCities() {
return this.cities.filter(city => city.id !== this.disabledCity);
}
}
});
