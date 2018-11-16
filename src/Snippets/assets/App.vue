<template>
    <div id="app">
        <router-view></router-view>
    </div>
</template>
<script>
import Vue from "vue";
import BootstrapVue from 'bootstrap-vue';
import ToggleButton from 'vue-js-toggle-button'
Vue.use(ToggleButton)
Vue.use(BootstrapVue);
export default {
    name: 'app',
    mounted() {
        this.setInitialData();
    },
    methods: {
        setInitialData() {
            var initial = JSON.parse(document.getElementById('initial_data').innerHTML);

            this.$store.commit('user', initial['user']);
            this.$store.commit('locale', initial['locale']);
            this.$store.commit('url', initial['url']);
        },
    },
}
</script>
<style lang="scss" src="assets/sass/bootstrap/bootstrap.scss"></style>
<style src="font-awesome/css/font-awesome.css"></style>
<style src="bootstrap-vue/dist/bootstrap-vue.css"></style>
