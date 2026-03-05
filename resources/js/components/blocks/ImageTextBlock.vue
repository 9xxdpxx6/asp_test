<template>
    <div class="image-text-block-wrapper">
        <h3 v-if="content.title" class="text-center mb-4 fw-bold">{{ content.title }}</h3>
        <div class="row align-items-start" :class="{ 'flex-row-reverse': content.layout === 'right' }">
            <div :class="imageColClass" class="mb-3 mb-md-0" v-if="content.image_url">
                <div class="image-frame rounded overflow-hidden">
                    <img :src="content.image_url" alt="" class="img-fluid" />
                </div>
            </div>
            <div :class="textColClass">
                <div class="category-description" v-html="safeHtml"></div>
            </div>
        </div>
    </div>
</template>

<script>
import DOMPurify from 'dompurify'

const sizeMap = {
    '3/4': { img: 'col-md-9', text: 'col-md-3' },
    '2/3': { img: 'col-md-8', text: 'col-md-4' },
    '1/2': { img: 'col-md-6', text: 'col-md-6' },
    '1/3': { img: 'col-md-4', text: 'col-md-8' },
    '1/4': { img: 'col-md-3', text: 'col-md-9' },
};

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
        imageColClass() {
            const size = this.content.image_size || '1/2';
            return (sizeMap[size] || sizeMap['1/2']).img;
        },
        textColClass() {
            const size = this.content.image_size || '1/2';
            return (sizeMap[size] || sizeMap['1/2']).text;
        },
    },
}
</script>

<style scoped>
.image-frame {
    max-height: 450px;
}

.image-frame img {
    width: 100%;
    height: 100%;
    max-height: 450px;
    object-fit: cover;
    display: block;
}
</style>
