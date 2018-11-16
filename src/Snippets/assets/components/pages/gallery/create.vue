<template>
    <div>
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="action_row">
                <a href="#/gallery" class="btn btn-primary">
                    Back
                </a>
            </div>
        </div>
        <vue-clip :options="options" :on-complete="complete" :on-init="init">
            <template slot="clip-uploader-action">
                <div>
                    <div class="dz-message">
                        <h2 class="text-center"> Click or Drag and Drop files here upload </h2>
                    </div>
                </div>
            </template>
            <template slot="clip-uploader-body" slot-scope="props">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                        <tr>
                            <th>Preview</th>
                            <th>Name</th>
                            <th>Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="file in props.files">
                            <td><img v-bind:src="file.dataUrl" v-if="file.dataUrl" />
                                <span v-else>No Preview Available</span></td>
                            <td>{{ file.name }}</td>
                            <td>{{ file.status }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="text-right">
                    <button class="btn btn-primary" @click="upload">Upload</button>
                </div>
            </template>
        </vue-clip>
    </div>
</template>
<script>
    import Vue from 'vue'
    import VueClip from 'vue-clip'
    import miniToastr from 'mini-toastr';
    miniToastr.init();

    Vue.use(VueClip)
    export default {

        data() {
            return {
                instance: "",
                options: {
                    url: this.$store.state.url + '/api/admin/media',
                    paramName: 'file',
                    autoProcessQueue: false,
                    maxFiles: {
                        limit: 5,
                        message: 'You can only upload a max of 5 files'
                    },
                    headers: { 'Authorization': "Bearer " + this.$store.state.user.token }
        }
            }
        },
        methods: {
            complete(file, status, xhr) {
                miniToastr.success("Operation Complete", "Success")
            },
            init(uploader) {
                this.instance = uploader.uploader._uploader;
                // console.log(uploader.uploader._uploader);
                // uploader.uploader._uploader.processQueue();
            },
            upload() {
                this.instance.processQueue();
            }
        }
    }
</script>
<style scoped>
    #action_row{
        margin-bottom: 30px;
    }

    .dz-message {
        min-height: 200px;
        padding-top: 3%;
        background-color: #f1f1f1;
        border: 2px dashed #ccc;
    }
</style>