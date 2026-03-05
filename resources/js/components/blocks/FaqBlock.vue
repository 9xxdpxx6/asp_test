<template>
    <div class="faq-block">
        <h3 v-if="content.title" class="text-center mb-4 fw-bold">{{ content.title }}</h3>
        <div class="faq-list">
            <div
                class="faq-item card mb-3"
                v-for="(item, index) in content.items"
                :key="index"
                :class="{ 'is-open': isOpen(index) }"
            >
                <button
                    class="faq-question btn w-100 text-start d-flex justify-content-between align-items-center p-4"
                    type="button"
                    @click="toggleItem(index)"
                >
                    <span class="fw-semibold">{{ item.question }}</span>
                    <i
                        class="fas fa-chevron-down faq-chevron ms-3"
                        :class="{ 'rotated': isOpen(index) }"
                    ></i>
                </button>
                <div v-show="isOpen(index)" class="faq-answer px-4 pb-4">
                    <hr class="mt-0 mb-3">
                    <p class="text-muted mb-0" style="line-height: 1.7;">{{ item.answer }}</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'FaqBlock',
    props: {
        content: {
            type: Object,
            required: true,
        },
    },
    data() {
        return {
            openItems: {},
        }
    },
    methods: {
        isOpen(index) {
            return !!this.openItems[index];
        },
        toggleItem(index) {
            this.openItems = {
                ...this.openItems,
                [index]: !this.isOpen(index),
            };
        },
    },
}
</script>

<style scoped>
.faq-item {
    border: 1px solid #e2e8f0;
    border-radius: 0.75rem !important;
    overflow: hidden;
    transition: box-shadow 0.2s ease;
}

.faq-item:hover {
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
}

.faq-item.is-open {
    border-color: var(--bs-primary);
    box-shadow: 0 2px 12px rgba(13, 110, 253, 0.1);
}

.faq-question {
    background: transparent;
    border: none;
    font-size: 1.05rem;
}

.faq-question:hover {
    background: #f8fafc;
}

.faq-chevron {
    font-size: 0.85rem;
    color: #94a3b8;
    transition: transform 0.25s ease;
    flex-shrink: 0;
}

.faq-chevron.rotated {
    transform: rotate(180deg);
}
</style>
