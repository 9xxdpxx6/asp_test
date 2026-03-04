<template>
    <div class="category-block" :class="'block-type-' + block.type">
        <component :is="blockComponent" :content="block.content" />
    </div>
</template>

<script>
import TextBlock from './TextBlock.vue'
import ImageBlock from './ImageBlock.vue'
import ImageTextBlock from './ImageTextBlock.vue'
import FeaturesBlock from './FeaturesBlock.vue'
import FaqBlock from './FaqBlock.vue'
import PricingBlock from './PricingBlock.vue'
import GalleryBlock from './GalleryBlock.vue'

const componentMap = {
    text: TextBlock,
    image: ImageBlock,
    image_text: ImageTextBlock,
    features: FeaturesBlock,
    faq: FaqBlock,
    pricing: PricingBlock,
    gallery: GalleryBlock,
}

export default {
    name: 'BlockRenderer',
    props: {
        block: {
            type: Object,
            required: true,
        },
    },
    computed: {
        blockComponent() {
            return componentMap[this.block.type] || null
        },
    },
}
</script>

<style scoped>
.category-block {
    margin-bottom: 2rem;
}
</style>
