<template>
    <a
        v-if="isExternal"
        :href="href"
        class="btn btn-lg rounded-pill px-4"
        :class="btnClass"
        target="_blank"
        rel="noopener noreferrer"
    >
        <i v-if="icon" :class="icon" class="me-2"></i>{{ label }}
    </a>
    <a
        v-else-if="isAnchor"
        :href="href"
        class="btn btn-lg rounded-pill px-4"
        :class="btnClass"
    >
        <i v-if="icon" :class="icon" class="me-2"></i>{{ label }}
    </a>
    <router-link
        v-else
        :to="href || '/'"
        class="btn btn-lg rounded-pill px-4"
        :class="btnClass"
    >
        <i v-if="icon" :class="icon" class="me-2"></i>{{ label }}
    </router-link>
</template>

<script>
export default {
    name: 'HeroCtaButton',
    props: {
        label: { type: String, default: '' },
        icon: { type: String, default: '' },
        href: { type: String, default: '' },
        variant: { type: String, default: 'light' },
    },
    computed: {
        btnClass() {
            if (this.variant === 'primary') {
                return 'btn-primary';
            }

            return this.variant === 'outline-light' ? 'btn-outline-light' : 'btn-light';
        },
        isExternal() {
            return /^https?:\/\//i.test(this.href || '');
        },
        isAnchor() {
            const h = this.href || '';
            return h.startsWith('#');
        },
    },
};
</script>
