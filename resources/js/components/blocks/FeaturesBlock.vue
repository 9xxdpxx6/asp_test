<template>
    <div class="features-block">
        <h3 v-if="content.title" class="text-center mb-4">{{ content.title }}</h3>
        <div class="row">
            <div class="col-md-4 mb-4" v-for="(item, index) in content.items" :key="index">
                <div class="card h-100 border-0 shadow-sm text-center p-3">
                    <div class="feature-icon mb-3">
                        <i :class="item.icon" class="fa-2x text-primary"></i>
                    </div>
                    <h5 class="card-title">{{ item.title }}</h5>
                    <p class="card-text text-muted">{{ item.text }}</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'FeaturesBlock',
    props: {
        content: {
            type: Object,
            required: true,
        },
    },
}
</script>

<style scoped>
.feature-icon {
    width: 60px;
    height: 60px;
    line-height: 60px;
    margin: 0 auto;
    border-radius: 50%;
    background: rgba(13, 110, 253, 0.1);
}
</style>
