<template>
    <div>
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="action_row">
                <a href="#/gallery-create" class="btn btn-primary">
                    Create
                </a>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <h3>
                    {{filterOption}} Media
                </h3>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 text-lg-right text-md-right  text-sm-right">
                <div class="btn-group">
                    <button v-for="(val, key) in option.getFilterData" class="btn" :class="[key===filterOption? 'is-checked' : '']" @click="filter(key)" :key="key">{{key}}
                    </button>
                </div>
            </div>
        </div>
        <isotope class="center-block" ref="cpt" id="isotope" :item-selector="'element-item'" :list="list" :options='option' v-images-loaded:on.progress="layout" @filter="filterOption=arguments[0]">
            <div v-for="(element,index) in list" :key="index" :class="element.filter">
                <a :href="'#/gallery-show/' + element.slug">
                    <p>{{element.name}}</p>
                    <img :src="element.src" class="img-fluid">
                </a>
            </div>
        </isotope>
        <b-pagination
                size="md"
                :total-rows="paginator.total"
                v-model="paginator.current_page"
                :per-page="paginator.per_page"
                v-on:change="reloadMedia"
                align="center"
        > </b-pagination>
    </div>
</template>
<script>
var unsub;
import isotope from 'vueisotope'
import imagesLoaded from 'vue-images-loaded'
import baguetteBox from "baguettebox.js";
import miniToastr from 'mini-toastr';
miniToastr.init();

require("baguettebox.js/dist/baguetteBox.min.css");
export default {
    directives: {
        imagesLoaded,
    },
    components: {
        isotope,
    },
    data() {
        return {
            list: [],
            filterOption: "All",
            option: {
                itemSelector: ".element-item",
                getFilterData: {
                    All() {
                        return true;
                    },
                    Landscape(el) {
                        return el.filter === "Landscape";
                    },
                    Studio(el) {
                        return el.filter === "Studio";
                    }
                }
            },
            paginator: {
                current_page: 1,
                per_page: 0,
                total: 0,
            },
        }

    },
    methods: {
        filter: function(key) {
            this.$refs.cpt.filter(key);
        },
        layout() {
            this.$refs.cpt.layout('masonry');
        },
        getMedia() {
            const vm = this;
            axios.get(
                this.$store.state.url + "/api/admin/media?page="+vm.paginator.current_page,
                { 'headers': { 'Authorization': "Bearer " + vm.$store.state.user.token } }
            ).then(response => {
                vm.list = [];
                var medias = response.data.data;
                vm.paginator  = response.data.meta.pagination;
                var media;
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
                miniToastr.error(error.message, "Failure")
            });
        },
        reloadMedia(e) {
            const vm = this;
            vm.paginator.current_page = e;
            vm.getMedia();
        },
    },
    mounted() {
        unsub = this.$store.subscribe((mutation, state) => {
            if (mutation.type == "left_menu") {
                setTimeout(() => {
                    this.$refs.cpt.layout('masonry');
                    setTimeout(() => {
                        this.$refs.cpt.layout('masonry');
                    }, 600)
                });
            }
        });
        baguetteBox.run('#isotope');
        this.getMedia();
    },
    beforeRouteLeave (to, from, next) {        unsub();        next();    },
}
</script>
<style scoped>
#action_row{
    margin-bottom: 30px;
}

.element-item {
    padding: 7px;
}

.element-item img {
    width: 300px;
}

.element-item p {
    text-align: center;
}

@media screen and (max-width: 1250px)and (min-width: 1100px) {
    .element-item img {
        width: 400px;
    }
}

@media screen and (max-width: 991px) and (min-width: 900px) {
    .element-item img {
        width: 270px;
    }
}

@media screen and (max-width: 899px) and (min-width: 775px) {
    .element-item img {
        width: 350px;
    }
}

@media screen and (max-width: 670px) and (min-width: 400px) {
    .element-item img {
        width: 270px;
    }
}

button.is-checked {
    background-color: #428bca;
    color: #fff;
}

button.btn {
    cursor: pointer;
}
</style>
