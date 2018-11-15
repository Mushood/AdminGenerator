<template>
    <div class="row">
        <div class="col-lg-12">
            <b-card header="Write new article" header-tag="h4" class="bg-primary-card">
                <form @submit.prevent="submitEntitygenerator">
                    <div class="row">
                        <div class="col-sm-12">
                            <a href="#/entitygenerator" class="btn btn-primary">
                                Back
                            </a>
                            <div class="pull-right" id="action_row">
                                <button class="btn btn-info" @click='resetEntitygenerator' v-if="edit">
                                    <i class="fa fa-retweet"></i>
                                </button>
                                <button class="btn btn-success" type="submit">
                                    <span v-if="edit">Update</span>
                                    <span v-if="!edit">Save</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12">
                            INJECT_CODE_HERE_1
                        </div>
                    </div>
                </form>
            </b-card>
        </div>
    </div>
</template>
<script>
    import Vue from 'vue';
    import VueQuillEditor from 'vue-quill-editor';
    Vue.component('media_selection', require('./../gallery/selection.vue'));
    import {
        Event
    } from 'vue-tables-2';
    import 'codemirror/keymap/sublime';
    import 'codemirror/mode/javascript/javascript.js'
    import miniToastr from 'mini-toastr';
    miniToastr.init();

    // require styles
    import 'quill/dist/quill.core.css'
    import 'quill/dist/quill.snow.css'
    import 'quill/dist/quill.bubble.css'

    Vue.use(VueQuillEditor);

    import VeeValidate from 'vee-validate';
    Vue.use(VeeValidate);

    export default {
        name: "create-entitygenerator",
        data() {
            return {
                INJECT_CODE_HERE_2
                upload: false,
                edit: false,
                list: [],
                code: 'const a = 10',
                //INJECT_CODE_HERE_7
                quilleditorOption: {
                    modules: {
                        toolbar: [['bold', 'italic', 'underline', 'strike',
                            { 'list': 'ordered'},
                            { 'list': 'bullet' },
                            { 'header': [1, 2, 3, 4, 5, 6, false] },
                            { 'color': ['#d3bc6c'] },
                            { 'align': [] },
                        ],
                            ['link']]
                    },
                },
                editorOptions: {
                    // codemirror options
                    tabSize: 4,
                    mode: 'text/javascript',
                    theme: 'monokai',
                    lineNumbers: true,
                    line: true,
                    keyMap: "sublime",
                    extraKeys: {
                        "Ctrl": "autocomplete"
                    },
                    foldGutter: true,
                    gutters: ["CodeMirror-linenumbers", "CodeMirror-foldgutter"],
                    styleSelectedText: true,
                    highlightSelectionMatches: {
                        showToken: /\w/,
                        annotateScrollbar: true
                    }
                },
            }
        },
        methods: {
            onEditorChange({
                               editor,
                               html,
                               text
                           }) {
                //this.entitygenerator.body = html
            },

            submitEntitygenerator: function () {
                const vm = this;
                this.$validator.validateAll().then((result) => {
                    if (result) {
                        if (vm.edit) {
                            axios.put(
                                this.$store.state.url + "/api/admin/entitygenerator/" + vm.entitygenerator.slug,
                                {
                                    entitygenerator: vm.entitygenerator,
                                },
                                {
                                    'headers': { 'Authorization': "Bearer " + vm.$store.state.user.token }
                                }
                            ).then(response => {
                                miniToastr.success("Operation Complete", "Success")
                                this.$router.push("/entitygenerator");
                            })
                            .catch(function(error) {
                                miniToastr.error(error.message, "Failure")
                            });
                        }

                        if (!vm.edit) {
                            axios.post(
                                this.$store.state.url + "/api/admin/entitygenerator", {
                                    entitygenerator: vm.entitygenerator
                                },
                                {
                                    'headers': { 'Authorization': "Bearer " + vm.$store.state.user.token }
                                }
                            ).then(response => {
                                miniToastr.success("Operation Complete", "Success")
                                this.$router.push("/entitygenerator");
                            })
                                .catch(function(error) {
                                    miniToastr.error(error.message, "Failure")
                                });
                        }
                    } else {
                        miniToastr.error("Please check form values", "Failure")
                    }
                });
            },

            loadEntitygenerator(slug) {
                const vm = this;
                axios.get(
                    this.$store.state.url + "/api/admin/entitygenerator/" + slug ,
                    { 'headers': { 'Authorization': "Bearer " + vm.$store.state.user.token } }
                ).then(response => {
                    vm.entitygenerator = response.data.data;
                    INJECT_CODE_HERE_3
                })
                .catch(function(error) {
                    miniToastr.error(error.message, "Failure")
                });
            },

            resetEntitygenerator() {
                INJECT_CODE_HERE_4
            },

            //INJECT_CODE_HERE_5
        },
        mounted() {
            const vm = this;

            if(vm.$route.params.slug) {
                vm.edit = true;
                vm.loadEntitygenerator(vm.$route.params.slug);
            }

            //INJECT_CODE_HERE_6
        },
    }
</script>
<style scoped>
    .ql-tooltip.ql-editing {
        z-index: 99;
    }

    .row_input {
        //margin-top: 5px;
        margin-bottom: 10px;
    }

    .dz-progress {
        background-color: #08aa80 !important;
    }

    .fa {
        cursor: pointer
    }

    .fa-plus {
        font-size: 100px;
        margin-top: 25px;
    }

    #action_row {
        margin-bottom: 15px;
    }

    .form-danger {
        border: 1px red solid;
    }

    .is-danger {
        color: red;
    }

    .help {
        padding: 10px;
    }
</style>