<template>
    <div class="row">
        <div class="col-lg-12">
            <b-card header="Write new article" header-tag="h4" class="bg-primary-card">
                <div class="row">
                    <div class="col-sm-12">
                        <a href="#/page" class="btn btn-primary">
                            Back
                        </a>
                        <div class="pull-right" id="action_row">
                            <button class="btn btn-info" @click='resetPage' v-if="edit">
                                <i class="fa fa-retweet"></i>
                            </button>
                            <button class="btn btn-success" @click='submitPage'>
                                <span v-if="edit">Update</span>
                                <span v-if="!edit">Save</span>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <div class="row_input">
                            <label for="locale" class="control-label">Language:</label>
                            <select id="locale" class="form-control" v-model="page.locale" :disabled="!edit" @change="getTranslatedIfExists">
                                <option value="en">English</option>
                                <option value="fr">French</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-6">
                        <div class="row_input"  v-if="page.menu != undefined">
                            <label for="title" class="control-label">Menu Title:</label>
                            <input id="title" type="text" class="form-control" placeholder="title" v-model="page.menu_title" :disabled="true"/>
                        </div>
                        <div class="row_input"  v-else>
                            <label for="title" class="control-label">Page ref:</label>
                            {{ page.ref }}
                        </div>
                        <div class="row_input">
                            <label for="page_title" class="control-label">Page Title:</label>
                            <input id="page_title" type="text" class="form-control" placeholder="title" v-model="page.page_title"/>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="row_input">
                            <label for="seo" class="control-label">SEO:</label>
                            <select id="seo" class="form-control" v-model="page.seo_custom">
                                <option :value="undefined">Don't index</option>
                                <option value="1">Default</option>
                                <option value="2">Custom</option>
                            </select>
                        </div>
                    </div>
                </div>
            </b-card>
        </div>

        <div class="col-lg-12" v-if="page.seo_custom == 2">
            <b-card header="SEO Management" header-tag="h4" class="bg-warning-card">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="row_input">
                            <label for="seo_title" class="control-label">SEO Title:</label>
                            <input id="seo_title" type="text" class="form-control" placeholder="title" v-model="page.seo_ob.seo_title"/>
                        </div>
                        <div class="row_input">
                            <label for="viewport" class="control-label">Viewport:</label>
                            <input id="viewport" type="text" class="form-control" placeholder="viewport" v-model="page.seo_ob.viewport"/>
                        </div>
                        <div class="row_input">
                            <label for="fb_sitename" class="control-label">OG Sitename:</label>
                            <input id="fb_sitename" type="text" class="form-control" placeholder="fb_sitename" v-model="page.seo_ob.fb_sitename"/>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="row_input">
                            <label for="fb_title" class="control-label">OG Title:</label>
                            <input id="fb_title" type="text" class="form-control" placeholder="title" v-model="page.seo_ob.fb_title"/>
                        </div>
                        <div class="row_input">
                            <label for="fb_type" class="control-label">
                                OG Type: <a href="https://developers.facebook.com/docs/reference/opengraph" target="_blank">More info</a>
                            </label>
                            <select id="fb_type" class="form-control" v-model="page.seo_ob.fb_type">
                                <option value="article">article</option>
                                <option value="book">book</option>
                                <option value="books.author">books.author</option>
                                <option value="books.book">books.book</option>
                                <option value="books.genre">books.genre</option>
                                <option value="business.business">business.business</option>
                                <option value="fitness.course">fitness.course</option>
                                <option value="game.achievement">game.achievement</option>
                                <option value="music.album">music.album</option>
                                <option value="music.playlist">music.playlist</option>
                                <option value="music.radio_station">music.radio_station</option>
                                <option value="music.song">music.song</option>
                                <option value="place">place</option>
                                <option value="product">product</option>
                                <option value="product.group">product.group</option>
                                <option value="product.item">product.item</option>
                                <option value="profile">profile</option>
                                <option value="restaurant.menu">restaurant.menu</option>
                                <option value="restaurant.menu_item">restaurant.menu_item</option>
                                <option value="restaurant.menu_section">restaurant.menu_section</option>
                                <option value="restaurant.restaurant">restaurant.restaurant</option>
                                <option value="video.episode">video.episode</option>
                                <option value="video.movie">video.movie</option>
                                <option value="video.other">video.other</option>
                                <option value="video.tv_show">video.tv_show</option>
                            </select>
                        </div>
                        <div class="row_input">
                            <label for="fb_url" class="control-label">OG URL:</label>
                            <input id="fb_url" type="text" class="form-control" placeholder="fb_url" v-model="page.seo_ob.fb_url"/>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="row_input">
                            <label for="seo_description" class="control-label">SEO Description:</label>
                            <input id="seo_description" type="text" class="form-control" placeholder="description" v-model="page.seo_ob.seo_description"/>
                        </div>
                        <div class="row_input">
                            <label for="fb_description" class="control-label">OG Description:</label>
                            <input id="fb_description" type="text" class="form-control" placeholder="description" v-model="page.seo_ob.fb_description"/>
                        </div>
                        <div class="row_input">
                            <label for="title" class="control-label">OG Image:</label>
                            <div class="row">
                                <div class="col-sm-3" v-if="page.seo_ob.fb_image">
                                    <img :src='page.seo_ob.fb_image' alt='image' class='img-fluid' />
                                </div>
                                <div class="col-sm-3 text-center">
                                    <i
                                            class='fa fa-plus text-info mr-3'
                                            v-b-modal.media_selection variant="outline-secondary"
                                            @click="notifyCategory(0)"
                                    ></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </b-card>
        </div>

        <div class="col-lg-12" v-if="page.blocks && page.blocks.length > 0">
            <b-card header="Blocks" header-tag="h4" class="bg-primary-card">
                <div class="row" v-for="(block,index) in page.blocks">
                    <div class="col-sm-3">
                        <div class="row_input">
                            <label :for="'block_type' + index" class="control-label">{{ block.name }}:</label>

                            <label :for="'block_type' + index" class="control-label">Block Type:</label>
                            <select :id="'block_type' + index"  class="form-control" v-model="block.type">
                                <option :value="block_type" v-for="block_type in page.block_types">{{ block_type }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-9">
                        <div class="row_input" v-if="block.type == 'STRING'">
                            <input :id="'title_' + index" type="text" class="form-control" placeholder="title" v-model="block.name" />
                        </div>
                        <div class="row_input" v-if="block.type == 'TEXT'">
                            <textarea class="form-control" v-model="block.desc" />
                        </div>
                        <div class="row_input" v-if="block.type == 'EDITOR'">
                            <quill-editor
                                    :content="block.desc"
                                    :options="quilleditorOption"
                                    ref="myTextEditor"

                                    class="edi"
                                    v-model="block.desc"
                                    id="content"
                            ></quill-editor>
                        </div>
                        <div class="row_input" v-if="block.type == 'MEDIA'">
                            <div class="row">
                                <div class="col-sm-3" v-if="block.file">
                                    <img :src='block.file' alt='image' class='img-fluid' />
                                </div>
                                <div class="col-sm-3 text-center">
                                    <i
                                            class='fa fa-plus text-info mr-3'
                                            v-b-modal.media_selection variant="outline-secondary"
                                            @click="notifyCategory(index+1)"
                                    ></i>
                                </div>
                            </div>
                        </div>
                        <div class="row_input" v-if="block.type == 'ALBUM'">
                            <div class="row">
                                <div class="col-sm-3" v-if="block.images" v-for="(image,iteration) in JSON.parse(block.images)" style="padding-bottom: 15px">
                                    <div class="row">
                                        <div class="col-sm-1">
                                            <i class="fa fa-trash text-danger" @click="removeFromSlider(index,iteration)"></i>
                                        </div>
                                        <div class="col-sm-10">
                                            <img :src='image[0]' :alt='image[1]' class='img-fluid' />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-3 text-center">
                                    <i
                                            class='fa fa-plus text-info mr-3'
                                            v-b-modal.media_selection variant="outline-secondary"
                                            @click="notifyCategory(index+1)"
                                    ></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </b-card>
        </div>

        <media_selection></media_selection>
    </div>
</template>
<script>
    import Vue from 'vue';
    import miniToastr from 'mini-toastr';

    import VueQuillEditor from 'vue-quill-editor';
    import 'quill/dist/quill.core.css'
    import 'quill/dist/quill.snow.css'
    import 'quill/dist/quill.bubble.css'
    Vue.use(VueQuillEditor);

    Vue.component('media_selection', require('./../gallery/selection.vue'));
    import {
        Event
    } from 'vue-tables-2';
    miniToastr.init();

    export default {
        name: "create-page",
        data() {
            return {
                page: {
                    name: undefined,
                    body: undefined,
                    file: undefined,
                    category: undefined,
                    file_slug: undefined,
                    locale: 'en',
                },
                original: {
                    name: undefined,
                    body: undefined,
                    file: undefined,
                    category: undefined,
                    file_slug: undefined,
                    locale: undefined,
                },
                temp_page: {
                    loaded: false,
                    file: undefined,
                    category: undefined,
                    file_slug: undefined,
                },
                upload: false,
                edit: false,
                list: [],
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
            notifyCategory: function(index) {
                Event.$emit('media_selection_start',{
                    index: index,
                });
            },

            setMedia: function(event) {
                console.log(event);
                if (event.index == 0) {
                    this.page.seo_ob[0].fb_image = event.src;
                }

                if (event.index > 0) {
                    if (this.page.blocks[event.index - 1].type == 'MEDIA') {
                        this.page.blocks[event.index - 1].file = event.src;
                        this.page.blocks[event.index - 1].file_slug = event.slug;
                    }

                    if (this.page.blocks[event.index - 1].type == 'ALBUM') {
                        let albums = "";
                        albums = JSON.parse(this.page.blocks[event.index - 1].images);
                        if(!albums) {
                            albums = [];
                        }
                        albums.push([event.src, event.image]);
                        this.page.blocks[event.index - 1].images = JSON.stringify(albums);
                    }
                }
            },

            submitPage: function () {
                const vm = this;
                console.log(vm.page);
                if (vm.edit) {
                    axios.put(
                        this.$store.state.url + "/api/admin/page/" + vm.$route.params.ref,
                        {
                            page: vm.page
                        },
                        {
                            'headers': { 'Authorization': "Bearer " + vm.$store.state.user.token }
                        }
                    ).then(response => {
                        miniToastr.success("Operation Complete", "Success");
                        //this.$router.push("/page");
                    })
                    .catch(function(error) {
                        miniToastr.error(error.message, "Failure")
                    });
                }

                if (!vm.edit) {
                    axios.post(
                        this.$store.state.url + "/api/admin/page", {
                            page: vm.page
                        },
                        {
                            'headers': { 'Authorization': "Bearer " + vm.$store.state.user.token }
                        }
                    ).then(response => {
                        miniToastr.success("Operation Complete", "Success")
                        this.$router.push("/page");
                    })
                    .catch(function(error) {
                        miniToastr.error(error.message, "Failure")
                    });
                }
            },

            loadPage(slug, locale = 'en') {
                const vm = this;
                axios.get(
                    this.$store.state.url + "/api/admin/page/" + slug + "/" + locale,
                    { 'headers': { 'Authorization': "Bearer " + vm.$store.state.user.token } }
                ).then(response => {
                    vm.page = response.data.data;
                    if(vm.page.seo_custom > 1) {
                        vm.page.seo_custom = 2;
                    }
                    vm.original.locale      = response.data.data.locale;

                    if(vm.temp_page.loaded) {
                        vm.loadTempInfo();
                    }

                    if (vm.page.menu == null) {
                        vm.page.menu = undefined;
                    }

                    if (vm.page.seo_custom == null) {
                        vm.page.seo_custom = undefined;
                    }


                    /*if (response.data.meta.type == "translation") {
                        vm.page.name = undefined;
                        vm.page.body = undefined;
                    }*/

                    if (vm.page.seo_ob == undefined) {
                        vm.page.seo_ob = {
                            seo_title: undefined,
                            fb_title: undefined,
                            seo_description: undefined,
                            fb_description: undefined,
                        };
                    }

                })
                .catch(function(error) {
                    miniToastr.error(error.message, "Failure")
                });
            },

            resetPage() {
                this.page.locale    = this.original.locale;
            },

            getTranslatedIfExists() {
                this.saveTempInfo();
                this.loadPage(this.$route.params.ref, this.page.locale);
            },

            saveTempInfo() {
                this.temp_page.loaded = true;
                this.temp_page.file = this.page.file;
                this.temp_page.file_slug = this.page.file_slug;
                this.temp_page.category = this.page.category;
            },

            loadTempInfo() {
                this.page.file = this.temp_page.file;
                this.page.file_slug = this.temp_page.file_slug;
                this.page.category = this.temp_page.category;
                this.temp_page.loaded = false;
            },

            removeFromSlider(index,iteration) {
                let album = JSON.parse(this.page.blocks[index].images);
                album.splice(iteration, 1);
                this.page.blocks[index].images = JSON.stringify(album);
            },
        },
        mounted() {
            const vm = this;
            Event.$on('media_selection_done', function(event){
                vm.setMedia(event);
            });

            if(vm.$route.params.ref) {
                vm.edit = true;
                vm.loadPage(vm.$route.params.ref);
            }
        },
    }
</script>
<style scoped>
    .ql-tooltip.ql-editing{
        z-index: 99;
    }

    .row_input {
        //margin-top: 5px;
        margin-bottom: 10px;
    }

    .dz-progress{
        background-color: #08aa80 !important;
    }

    .fa {
        cursor: pointer
    }

    .fa-plus {
        font-size: 100px;
        margin-top: 25px;
    }

    #action_row{
        margin-bottom: 15px;
    }
</style>