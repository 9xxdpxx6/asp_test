<template>
    <div class="image-text-block-wrapper">
        <h3 v-if="content.title" class="text-center mb-4">{{ content.title }}</h3>
        <div class="row align-items-center" :class="{ 'flex-row-reverse': content.layout === 'right' }">
            <div class="col-md-6 mb-3 mb-md-0" v-if="content.image_url">
                <img :src="content.image_url" alt="" class="img-fluid rounded shadow-sm" />
            </div>
            <div class="col-md-6">
                <div class="category-description" v-html="safeHtml"></div>
            </div>
        </div>
    </div>
</template>

<script>
import DOMPurify from 'dompurify'

export default {
    name: 'ImageTextBlock',
    props: {
        content: {
            type: Object,
            required: true,
        },
    },
    computed: {
        safeHtml() {
            return this.content.html ? DOMPurify.sanitize(this.content.html) : ''
        },
    },
}
</script>

<style scoped>
.image-text-block-wrapper img {
    max-width: 100%;
    height: auto;
}
</style>
