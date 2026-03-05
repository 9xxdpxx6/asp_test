<template>
    <div class="features-block">
        <h3 v-if="content.title" class="text-center mb-4 fw-bold">{{ content.title }}</h3>
        <div class="row g-3">
            <div class="col-sm-6 col-lg-4 mb-3" v-for="(item, index) in content.items" :key="index">
                <div class="card h-100 text-center p-4 feature-card">
                    <div class="feature-icon mx-auto mb-3">
                        <i :class="item.icon"></i>
                    </div>
                    <h5 class="card-title fw-semibold mb-2">{{ item.title }}</h5>
                    <p class="card-text text-muted small mb-0">{{ item.text }}</p>
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
.feature-card {
    border: 1px solid #e2e8f0;
    border-radius: 0.75rem;
    transition: box-shadow 0.2s ease;
}

.feature-card:hover {
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
}

.feature-icon {
    width: 56px;
    height: 56px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: rgba(13, 110, 253, 0.1);
    font-size: 1.4rem;
    color: var(--bs-primary);
}
</style>
