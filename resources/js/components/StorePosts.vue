UpdatePosts.vue<template>
    <div>
        <input v-model="title" type="text" class="mb-3 from-control" placeholder="title">

        <div ref="dropzone" class="mb-3 btn d-block p-5 bg-dark text-center text-light">
            Upload
        </div>

        <input @click.prevent="store" type="submit" class="mb-3 btn btn-primary" value="add">

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
    name: "Store",

    data() {
        return {
            dropzone: null,
            title: null,
            post: null,
            content: null,
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

        this.getPosts()
    },

    methods: {
        store() {
            const data = new FormData();
            const files = this.dropzone.getAcceptedFiles();
            files.forEach(file => {
                data.append('images[]', file)
                this.dropzone.removeFile(file)
            })
            data.append('title', this.title)
            data.append('content', this.content)
            this.title = ''
            this.content = ''
            axios.post('/api/posts', data)
        },
        getPosts() {
            axios.get('/api/posts')
                .then(res => {
                        this.post = res.data.data
                    }
                )
        },

        handleImageAdded(file, Editor, cursorLocation, resetUploader) {
            var formData = new FormData();
            formData.append("image", file);

            axios({
                url: "https://fakeapi.yoursite.com/images",
                method: "POST",
                data: formData
            })
                .then(result => {
                    const url = result.data.url; // Get url from response
                    Editor.insertEmbed(cursorLocation, "image", url);
                    resetUploader();
                })
                .catch(err => {
                    console.log(err);
                });
        }

    },
}
</script>


<style scoped>

</style>
