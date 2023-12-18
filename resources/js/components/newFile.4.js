/* __placeholder__ */
export default (await import('vue')).defineComponent({
data() {
return {
selectedDate: this.dateToString(new Date())
};
},

props: {
title: String,
startDate: String | null,
endDate: String | null,
updateDate: Function
},

methods: {
dateToString(date) {
return date.toISOString().split("T")[0];
},

testFunction() { }
}
});
