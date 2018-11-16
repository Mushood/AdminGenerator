<template>
    <div class="row">
        <div class="col-lg-12">
            <b-card header="Edit Media" header-tag="h4" class="bg-default-card">
                <div class="row">
                    <div class="col-sm-2">
                        <a href="#/gallery" class="btn btn-primary">
                            Back
                        </a>
                    </div>

                    <div class="col-sm-8"></div>

                    <div class="col-sm-2">
                        <div class="form-group pull-right">
                            <input type="submit" value="Delete" class="btn btn-danger" @click="deleteMedia" />
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <vue-form :state="formstate" @submit.prevent="onSubmit">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <validate tag="div">
                                            <label for="name">File Name</label>
                                            <input
                                                    v-model="media.file"
                                                    name="name"
                                                    type="text"
                                                    required
                                                    autofocus
                                                    placeholder="File Name"
                                                    class="form-control"
                                                    id="name"
                                            />
                                            <field-messages name="name" show="$invalid && $submitted" class="text-danger">
                                                <div slot="required">Name is a required field</div>
                                            </field-messages>
                                        </validate>
                                    </div>
                                </div>

                                <div class="col-sm-12">
                                    <div class="form-group float-right">
                                        <input type="submit" value="Update" class="btn btn-success" />
                                    </div>
                                </div>

                                <div class="col-sm-12">
                                    <img :src="media.url" class="img-fluid">
                                </div>
                            </div>
                        </vue-form>
                    </div>
                </div>
            </b-card>
        </div>
    </div>
</template>
<script>
    import Vue from 'vue'

    import VueForm from "vue-form";
    import options from "src/validations/validations.js";
    import swal from 'sweetalert2';
    import miniToastr from 'mini-toastr';
    miniToastr.init();
    Vue.use(VueForm, options);
    export default {
        name: "formfeatures",
        data() {
            return {
                name: "",
                formstate: {},
                media: {
                    file: "",
                    url: ""
                }
            }
        },
        methods: {
            onSubmit: function() {
                if (this.formstate.$invalid) {
                    return;
                } else {
                    const vm = this;
                    var url = this.$route.params.url;
                    axios.put(
                        this.$store.state.url + "/api/admin/media/" + url,
                        {
                            file: vm.media.file,
                        },
                        {
                            'headers': { 'Authorization': "Bearer " + vm.$store.state.user.token }
                        }
                    ).then(response => {
                        miniToastr.success("Operation Complete", "Success")
                        this.$router.push("/gallery");
                    })
                    .catch(function(error) {
                        miniToastr.error(error.message, "Failure")
                    });
                }
            },

            getMedium: function(url) {
                const vm = this;
                axios.get(
                    this.$store.state.url + "/api/admin/media/" + url,
                    { 'headers': { 'Authorization': "Bearer " + vm.$store.state.user.token } }
                ).then(response => {
                    vm.media = response.data.data;
                })
                .catch(function(error) {
                    miniToastr.error(error.message, "Failure")
                });
            },

            deleteMedia: function() {
                const vm = this;
                var url = this.$route.params.url;

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
                            this.$store.state.url + "/api/admin/media/" + url,
                            { 'headers': { 'Authorization': "Bearer " + vm.$store.state.user.token } }
                        ).then(response => {
                            if (response.data.errors) {
                                swal(
                                    'Error!',
                                    response.data.errors.message,
                                    'question'
                                )
                            } else {
                                miniToastr.warn("Operation Complete", "Success")
                                this.$router.push("/gallery");
                            }
                        })
                        .catch(function(error) {
                            miniToastr.error(error.message, "Failure")
                        });
                    }
                });
            }

        },
        mounted: function() {
            this.getMedium(this.$route.params.url);
        },
        destroyed: function() {

        }
    }
</script>
<style>
    .form-control{
        transition: initial;
    }
</style>