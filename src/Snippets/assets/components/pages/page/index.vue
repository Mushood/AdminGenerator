<template>
    <div>
        <div class="row">
            <div class="col-lg-12 mb-3">
                <b-card header="All pages" header-tag="h4" class="bg-info-card">
                    <div class="pull-right">
                        <select id="locale" class="form-control" v-model="locale" @change="getPages">
                            <option value="en">English</option>
                            <option value="fr">French</option>
                        </select>
                    </div>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Menu</th>
                                <th scope="col">Menu Title</th>
                                <th scope="col">Sub Menu</th>
                                <th scope="col">SEO Type</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(page,index) in pages">
                                <th scope="row">{{ index+1 }}</th>
                                <td>
                                    <span>
                                        <select id="menu" class="form-control" v-model="page.menu">
                                            <option :value="undefined">Unlinked</option>
                                            <option value="0">Menu</option>
                                            <option value="1">Sub Menu</option>
                                        </select>
                                    </span>
                                </td>
                                <td>
                                    <span v-if="page.menu != undefined">
                                        <input id="menu_title" type="text" class="form-control" placeholder="title" v-model="page.menu_title" />
                                    </span>
                                    <span v-else>
                                        {{ page.ref }}
                                    </span>
                                </td>
                                <td>
                                    <span v-if="page.menu == 1">
                                        <select id="submenu" class="form-control" v-model="page.submenu">
                                            <option :value="page.ref" v-for="page in pagemenu">{{ page.ref }}</option>
                                        </select>
                                    </span>
                                </td>
                                <td>
                                    <span v-if="page.seo == undefined">
                                        Don't index
                                    </span>
                                    <span v-if="page.seo == 1">
                                        Default
                                    </span>
                                    <span v-if="page.seo > 1">
                                        Custom
                                    </span>
                                </td>
                                <td>
                                    <i class='fa fa-pencil text-info mr-3' @click='editPage(page)'></i>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </b-card>
            </div>
        </div>
        <div class="row">
            <b-card class="pt-1 bg-primary" variant="primary" id="visual_menu">
                <div class="row">
                    <div class=" col-sm-8">
                        <h5 class="text-white">Visual Menu</h5>
                    </div>
                    <div class="col-sm-4">
                        <button class="btn btn-success pull-right" @click="updateMenu"> Update </button>
                    </div>
                </div>
            </b-card>
        </div>
        <div class="row">
            <div class="col-lg-3 mb-3" v-for="page in pagemenu">
                <b-card :header="page.menu_title" header-tag="h4" class="bg-info-card">
                    <ul>
                        <li v-for="submenu in getSubmenu(page)">{{ submenu.menu_title }}</li>
                    </ul>
                </b-card>
            </div>
        </div>
    </div>
</template>
<script>
    import Vue from 'vue';
    import swal from 'sweetalert2';
    import {
        ClientTable,
        Event
    } from 'vue-tables-2';
    import datatable from "components/plugins/DataTable/DataTable.vue";
    import VModal from 'vue-js-modal';
    import miniToastr from 'mini-toastr';
    miniToastr.init();
    Vue.use(ClientTable, {}, false);
    Vue.use(VModal);
    Vue.component('media_selection', require('./../gallery/selection.vue'));
    export default {
        name: "advanced_tables",
        components: {
            datatable
        },
        data() {
            return {
                pages: [],
                locale: 'en',
            }
        },
        computed: {
            pagemenu: function () {
                return this.pages.filter(function(page) {
                    return page.menu == 0;
                });
            },
        },
        methods: {
            getPages() {
                const vm = this;
                axios.get(
                    this.$store.state.url + "/api/admin/page/" + vm.locale,
                    { 'headers': { 'Authorization': "Bearer " + vm.$store.state.user.token } }
                ).then(response => {
                    vm.pages = response.data.data;

                    for(var i = 0; i < vm.pages.length; i++) {

                        if (vm.pages[i].menu == null) {
                            vm.pages[i].menu = undefined;
                        }
                        if (vm.pages[i].menu != undefined) {
                            if (vm.pages[i].menu > 0) {
                                vm.pages[i].menu    = 1;
                            }
                        }
                    }
                })
                .catch(function(error) {
                    miniToastr.error(error.message, "Failure")
                });
            },

            getSubmenu(selected_page) {
                return this.pages.filter(function(page) {
                    return page.menu > 0 && page.submenu == selected_page.ref;
                });
            },

            reloadPages(e) {
                const vm = this;
                vm.paginator.current_page = e;
                vm.getPages();
            },

            editPage(page) {
                this.$router.push("/page/" + page.ref);
            },

            updateMenu() {
                const vm = this;
                axios.post(
                    this.$store.state.url + "/api/admin/menu/update",
                    { pages: vm.pages, language: vm.locale },
                    { 'headers': { 'Authorization': "Bearer " + vm.$store.state.user.token } }
                ).then(response => {
                    miniToastr.success("Operation Complete", "Success");
                })
                .catch(function(error) {
                    miniToastr.error(error.message, "Failure")
                });
            },
        },
        mounted() {
            const vm = this;
            vm.getPages();
        }
    }
</script>
<style scoped>
    .page_image{
        max-width: 100px;
    }

    .fa{
        cursor: pointer;
    }

    #action_row{
        margin-bottom: 15px;
    }

    .page_media a p{
        margin-bottom: 1px;
        text-align: center;
    }

    #visual_menu{
        width: 100%;
    }

    .card-body{
        padding: 0.5rem;
    }

    .card-body h5{
        font-size: 1.5rem;
        padding-left: 1.5rem;
    }
</style>