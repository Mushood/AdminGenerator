<template>
    <div>
        <div class="row">
            <div class="col-lg-12 mb-3">
                <b-card header="All entitygenerators" header-tag="h4" class="bg-info-card">
                    <div class="pull-right" id="action_row">
                        <a class="btn btn-success" href="#/entitygenerator-create">
                            Add New
                        </a>
                    </div>
                    <table class="table table-striped">
                        INJECT_CODE_HERE_1
                        <tbody>
                            <tr v-for="(entitygenerator,index) in entitygenerators">
                                INJECT_CODE_HERE_2
                            </tr>
                        </tbody>
                    </table>
                    <b-pagination
                            size="md"
                            :total-rows="paginator.total"
                            v-model="paginator.current_page"
                            :per-page="paginator.per_page"
                            v-on:change="reloadEntitygenerators"
                            align="center"
                    > </b-pagination>
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
                entitygenerators: [],
                selected_media: {
                    name: undefined,
                    src: undefined
                },
                list: [],
                paginator: {
                    current_page: 1,
                    per_page: 0,
                    total: 0,
                },
            }
        },
        methods: {
            getEntitygenerators() {
                const vm = this;
                axios.get(
                    this.$store.state.url + "/api/admin/entitygenerator?page="+vm.paginator.current_page,
                    { 'headers': { 'Authorization': "Bearer " + vm.$store.state.user.token } }
                ).then(response => {
                    vm.entitygenerators = response.data.data;
                    vm.paginator  = response.data.meta.pagination;
                })
                .catch(function(error) {
                    miniToastr.error(error.message, "Failure")
                });
            },

            reloadEntitygenerators(e) {
                const vm = this;
                vm.paginator.current_page = e;
                vm.getEntitygenerators();
            },

            editEntitygenerator(entitygenerator) {
                this.$router.push("/entitygenerator/" + entitygenerator.slug);
            },

            deleteEntitygenerator: function(entitygenerator) {
                const vm = this;

                swal({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'No, cancel!',
                    reverseButtons: true
                }).then((result) => {
                    if (result.value) {
                        axios.delete(
                            this.$store.state.url + "/api/admin/entitygenerator/" + entitygenerator.slug,
                            { 'headers': { 'Authorization': "Bearer " + vm.$store.state.user.token } }
                        ).then(response => {
                            this.getEntitygenerators();
                            miniToastr.warn("Operation Complete", "Success")
                        })
                        .catch(function(error) {
                            miniToastr.error(error.message, "Failure")
                        });
                    }
                });
            },
        },
        mounted() {
            const vm = this;
            vm.getEntitygenerators();
        }
    }
</script>
<style>
    .entitygenerator_image{
        max-width: 100px;
    }

    .fa{
        cursor: pointer;
    }

    #action_row{
        margin-bottom: 15px;
    }

    .entitygenerator_media a p{
        margin-bottom: 1px;
        text-align: center;
    }
</style>