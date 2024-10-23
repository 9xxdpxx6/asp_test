<template>
    <div>
        <input v-model="title" type="text" class="mb-3 from-control" placeholder="title">

        <div ref="dropzone" class="mb-3 btn d-block p-5 bg-dark text-center text-light">
            Upload
        </div>

        <input @click.prevent="update" type="submit" class="mb-3 btn btn-primary" value="update">

        <div class="mb-3">
            <vue-editor
                useCustomImageHandler
                @image-added="handleImageAdded"
                v-model="content">
            </vue-editor>
        </div>

        <div class="mt-5">
            <div v-if="post">
                <h4>{{ post.title }}</h4>
                <div v-for="image in post.images">
                    <img :src="image.path">
                </div>
                <div class="ql-editor" v-html="post.content"></div>
            </div>
        </div>
    </div>
</template>

<script>
import {Dropzone} from "dropzone";
import {VueEditor} from "vue2-editor";

export default {
    name: "Update",

    data() {
        return {
            dropzone: null,
            title: null,
            post: null,
            content: null,
            imageIdsForDelete: [],
            imageUrlsForDelete: [],
        }
    },

    components: {
        VueEditor
    },

    mounted() {
        this.dropzone = new Dropzone(this.$refs.dropzone, {
            url: '/api/posts',
            autoProcessQueue: false,
            addRemoveLinks: true,
        })

        this.dropzone.on('removedfile',(file)=>{
            this.imageIdsForDelete.push(file.id)
        })
        this.getPosts()
    },

    methods: {
        update() {
            const data = new FormData();
            const files = this.dropzone.getAcceptedFiles();
            files.forEach(file => {
                data.append('images[]', file)
                this.dropzone.removeFile(file)
            })

            this.imageIdsForDelete.forEach(idForDelete=>{
                data.append('image_ids_for_delete[]', idForDelete)
            })
            this.imageUrlsForDelete.forEach(urlsForDelete=>{
                data.append('image_urls_for_delete[]', urlsForDelete)
            })

            data.append('title', this.title)
            data.append('content', this.content)
            data.append('_method', 'PATCH')
            this.title = ''
            this.content = ''
            axios.post(`/api/posts/${this.post.id}`, data)
                .then(res => {
                    let previews = this.dropzone.previewsContainer.querySelectorAll('.dz-image-preview')

                    previews.forEach(previews=>{
                        preview.remove()
                    })
                    this.getPosts()
                })
        },

        getPosts() {
            axios.get('/api/posts')
                .then(res => {
                        this.post = res.data.data

                        this.title = this.post.title
                        this.content = this.post.content

                        this.post.images.forEach(image => {
                            let file = {id: image.id,name: image.name, size: image.size};
                            this.dropzone.displayExistingFile(file, image.path);
                        })

                    })
        },


        handleImageAdded(file, Editor, cursorLocation, resetUploader) {
            var formData = new FormData();
            formData.append("image", file);

            axios.post("/api/posts/images", formData)
                .then(result => {
                    const url = result.data.url; // Get url from response
                    Editor.insertEmbed(cursorLocation, "image", url);
                    resetUploader();
                })
                .catch(err => {
                    console.log(err);
                });
        },

        handleImageRemoved(url){
            this.imageUrlsForDelete.push(url);
        }
    }
}
</script>


<style scoped>

</style>
