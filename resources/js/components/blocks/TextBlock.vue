<template>
    <div class="text-block category-description" v-html="safeHtml"></div>
</template>

<script>
import DOMPurify from 'dompurify'

export default {
    name: 'TextBlock',
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

<style>
.text-block.category-description img {
    max-width: 100%;
    height: auto;
    display: block;
    margin: 0 auto;
}
.text-block.category-description {
    max-width: 100%;
    overflow: hidden;
    line-height: 1.7;
}
.text-block .ql-align-center { text-align: center; }
.text-block .ql-align-right { text-align: right; }
.text-block .ql-align-justify { text-align: justify; }
</style>
