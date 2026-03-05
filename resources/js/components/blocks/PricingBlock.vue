<template>
    <div class="pricing-block">
        <h3 v-if="content.title" class="text-center mb-4 fw-bold">{{ content.title }}</h3>
        <div class="card pricing-card overflow-hidden">
            <div class="table-responsive">
                <table class="table table-striped mb-0 align-middle">
                    <tbody>
                        <tr v-for="(item, index) in content.items" :key="index">
                            <td class="ps-4 py-3 fw-semibold text-dark">{{ item.label }}</td>
                            <td class="pe-4 py-3 text-end text-primary fw-medium">{{ item.value }}</td>
                        </tr>
                    </tbody>
                </table>
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
    border: 1px solid #e2e8f0;
    border-radius: 0.75rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
}

.table > tbody > tr > td {
    font-size: 1.05rem;
    border-color: #f0f4f8;
}

.table > tbody > tr:last-child > td {
    border-bottom: none;
}

.table-striped > tbody > tr:nth-of-type(odd) > td {
    background-color: #f8fafc;
}
</style>
