<template>
    <div class="pricing-block">
        <h3 v-if="content.title" class="text-center mb-4">{{ content.title }}</h3>
        <div class="pricing-card">
            <div class="pricing-row" v-for="(item, index) in content.items" :key="index"
                 :class="{ 'pricing-row-alt': index % 2 === 1 }">
                <div class="pricing-label">
                    <span>{{ item.label }}</span>
                </div>
                <div class="pricing-value">{{ item.value }}</div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'PricingBlock',
    props: {
        content: {
            type: Object,
            required: true,
        },
    },
}
</script>

<style scoped>
.pricing-card {
    border-radius: 0.75rem;
    overflow: hidden;
    border: 1px solid #e2e8f0;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
}
.pricing-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 1.5rem;
}
.pricing-row:not(:last-child) {
    border-bottom: 1px solid #f0f0f0;
}
.pricing-row-alt {
    background-color: #f8fafc;
}
.pricing-label {
    display: flex;
    align-items: center;
    font-weight: 600;
    color: #1e293b;
    font-size: 1.05rem;
}
.pricing-value {
    font-weight: 500;
    color: #475569;
    font-size: 1.05rem;
    text-align: right;
    white-space: nowrap;
}

@media (max-width: 576px) {
    .pricing-row {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.25rem;
        padding: 0.75rem 1rem;
    }
    .pricing-value {
        text-align: left;
        color: #64748b;
    }
}
</style>
