<template>
    <div class="image-block" v-if="content.url">
        <h3 v-if="content.title" class="text-center mb-4">{{ content.title }}</h3>
        <figure class="text-center">
            <img :src="content.url" :alt="content.alt || ''" class="img-fluid rounded shadow-sm" />
            <figcaption v-if="content.caption" class="mt-2 text-muted">
                {{ content.caption }}
            </figcaption>
        </figure>
    </div>
</template>

<script>
export default {
    name: 'ImageBlock',
    props: {
        content: {
            type: Object,
            required: true,
        },
    },
}
</script>

<style scoped>
.image-block img {
    max-width: 100%;
    height: auto;
}
</style>
