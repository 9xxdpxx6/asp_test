<template>
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item">
                <router-link :to="{ name: 'home' }">Главная</router-link>
            </li>
            <li
                v-for="(crumb, index) in crumbs"
                :key="index"
                class="breadcrumb-item"
                :class="{ active: index === crumbs.length - 1 }"
                :aria-current="index === crumbs.length - 1 ? 'page' : undefined"
            >
                <router-link
                    v-if="index < crumbs.length - 1 && crumb.to"
                    :to="crumb.to"
                >
                    {{ crumb.label }}
                </router-link>
                <span v-else>{{ crumb.label }}</span>
            </li>
        </ol>
    </nav>
</template>

<script>
export default {
    name: 'Breadcrumbs',
    props: {
        crumbs: {
            type: Array,
            required: true,
            // Each crumb: { label: String, to: Object|null }
        },
    },
};
</script>

<style scoped>
.breadcrumb {
    background: transparent;
    padding: 0;
    font-size: 0.875rem;
}
</style>
