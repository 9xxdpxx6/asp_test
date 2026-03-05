<template>
    <section class="trust-bar py-3">
        <div class="container">
            <div class="d-flex flex-wrap justify-content-center align-items-center gap-3 gap-md-4">
                <div class="trust-item" v-for="(item, index) in items" :key="index">
                    <i :class="item.icon" class="text-primary me-2"></i>
                    <span v-if="item.value" class="fw-bold text-primary me-1">{{ item.value }}</span>
                    <span class="fw-semibold">{{ item.label }}</span>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
export default {
    name: 'TrustBar',
    data() {
        return {
            items: [
                { icon: 'fas fa-map-marker-alt', value: '2', label: 'филиала в Краснодаре' },
                { icon: 'fas fa-shield-alt', value: '', label: 'Гос. лицензия' },
                { icon: 'fas fa-user-tie', value: '', label: 'Опытные инструкторы' },
                { icon: 'fas fa-car', value: '', label: 'Современный автопарк' },
            ],
        };
    },
};
</script>

<style scoped>
.trust-bar {
    background: #f0f4ff;
    border-top: 1px solid #e2e8f0;
    border-bottom: 1px solid #e2e8f0;
}

.trust-item {
    display: flex;
    align-items: center;
    white-space: nowrap;
    font-size: 0.95rem;
}
</style>
