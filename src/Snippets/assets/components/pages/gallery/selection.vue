<template>
    <div>
        <!-- Modal Component -->
        <b-modal
                id="media_selection"
                title="Media Selection"
                size="lg"
        >
            <div class="row">
                <div class="col-sm-3">
                    <h2>Selection:</h2>
                </div>
                <div class="col-sm-9">
                    <p v-if="selected_media.name">{{ selected_media.name }}</p>
                    <img v-if="selected_media.src" :src='selected_media.src' alt='image' class='category_image' />
                </div>
            </div>
            <hr />
            <div class="row">
                <div class="col-sm-12" v-if="upload">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="pull-right">
                                <i class="fa fa-times-circle" @click="upload = false"></i>
                            </div>
                        </div>
                    </div>
                    <vue-dropzone
                            ref="product_attachment"
                            id="dropzone"
                            :options="dropzoneOptions"
                            @vdropzone-success="uploaded"
                    ></vue-dropzone>
                </div>
                <div class="col-sm-12" v-if="!upload">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="pull-right">
                                <i class="fa fa-upload" @click="upload = true"></i>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3 category_media"v-for="(element,index) in list" :key="index" @click="selectMedia(element)">
                            <p>{{element.name}}</p>
                            <img :src="element.src" class="img-fluid">
                        </div>
                    </div>
                    <b-pagination
                            size="md"
                            :total-rows="paginator.total"
                            v-model="paginator.current_page"
                            :per-page="paginator.per_page"
                            v-on:change="reloadMedia"
                            align="center"
                    > </b-pagination>
                </div>
            </div>
        </b-modal>
    </div>
</template>
<script>
    import Vue from 'vue';
    import VModal from 'vue-js-modal';
    import vue2Dropzone from 'vue2-dropzone'
    import 'vue2-dropzone/dist/vue2Dropzone.css'
    import {
        Event
    } from 'vue-tables-2';
    Vue.use(VModal);

    export default {
        name: "media-selection",
        components: {
            vueDropzone: vue2Dropzone,
        },
        data() {
            return {
                selected_media: {
                    name: undefined,
                    src: undefined,
                    slug: undefined
                },
                list: [],
                index: "",
                upload: false,
                dropzoneOptions:{
                    url: this.$store.state.url + '/api/admin/media',
                    thumbnailWidth: 150,
                    // maxFilesize: 0.5,
                    maxFiles:3,
                    headers: { 'Authorization': "Bearer " + this.$store.state.user.token }
                },
                paginator: {
                    current_page: 1,
                    per_page: 0,
                    total: 0,
                },
            }
        },
        methods: {
            getMedia() {
                const vm = this;
                axios.get(
                    this.$store.state.url + "/api/admin/media?page="+vm.paginator.current_page,
                    { 'headers': { 'Authorization': "Bearer " + vm.$store.state.user.token } }
                ).then(response => {
                    var medias = response.data.data;
                    vm.paginator  = response.data.meta.pagination;
                    var media;
                    vm.list = [];
                    for (media in medias) {
                        vm.list.push(
                            {
                                src: medias[media]['url'],
                                filter: "Landscape",
                                slug: medias[media]['slug'],
                                name: medias[media]['file']
                            }
                        );
                    }
                })
                .catch(function(error) {

                });
            },

            reloadMedia(e) {
                const vm = this;
                vm.paginator.current_page = e;
                vm.getMedia();
            },

            selectMedia(element) {
                const vm = this;
                vm.selected_media.name = element.name;
                vm.selected_media.src  = element.src;

                Event.$emit('media_selection_done',{
                    index: vm.index,
                    name: element.name,
                    slug: element.slug,
                    src: element.src,
                    image: element.image
                });
            },

            uploaded() {
                this.upload = false;
                this.getMedia();
            },
        },
        mounted() {
            const vm = this;
            vm.getMedia();
            Event.$on('media_selection_start', function(event){
                vm.selected_media.name = undefined;
                vm.selected_media.src = undefined;
                vm.index = event.index;
            });
        }
    }
</script>
<style>
    .category_image{
        max-width: 100px;
    }

    .category_media{
        cursor: pointer;
    }

    .category_media a p{
        margin-bottom: 1px;
        text-align: center;
    }

    .category_media p{
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>