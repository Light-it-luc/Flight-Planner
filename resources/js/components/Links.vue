<script>
    export default {
        props: {
            links: Array,
        },

        methods: {
            changePage(url) {
                const urlParams = url.split('?')[1]
                const params = new URLSearchParams(urlParams)
                this.$emit("update:queryParams", params.toString())
            }
        }
    }
</script>

<template>
     <div class="mt-12 mb-4 px-12">
        <button
            class="text-black p-1 border border-gray-500 min-w-fit px-4 rounded-lg m-2"
            :class="(link.active) ? 'bg-gray-300' : 'bg-white'"
            v-for="link in links"
            :key="link.label"
            :url="(link.url) ? link.url : null"
            v-on="(link.url) ? { click: () => changePage(link.url) } : {} "
            v-html="link.label"
    ></button>
     </div>
</template>
