<template>
    <div class="faq-block">
        <h3 v-if="content.title" class="text-center mb-4">{{ content.title }}</h3>
        <div class="accordion" :id="'faq-accordion-' + uid">
            <div class="accordion-item" v-for="(item, index) in content.items" :key="index">
                <h2 class="accordion-header" :id="'faq-heading-' + uid + '-' + index">
                    <button class="faq-question-btn"
                            :class="['accordion-button', { collapsed: !isOpen(index) }]"
                            type="button"
                            @click="toggleItem(index)"
                            :aria-expanded="isOpen(index) ? 'true' : 'false'"
                            :aria-controls="'faq-collapse-' + uid + '-' + index">
                        {{ item.question }}
                    </button>
                </h2>
                <div :id="'faq-collapse-' + uid + '-' + index"
                     class="accordion-collapse"
                     :class="{ collapse: true, show: isOpen(index) }"
                     :aria-labelledby="'faq-heading-' + uid + '-' + index">
                    <div class="accordion-body faq-answer-text">
                        {{ item.answer }}
                    </div>
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
            uid: Math.random().toString(36).substr(2, 9),
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
.accordion-button:not(.collapsed) {
    background-color: rgba(13, 110, 253, 0.05);
    color: #0d6efd;
}
.faq-question-btn {
    font-size: 1.15rem;
    font-weight: 600;
}
.faq-answer-text {
    font-style: italic;
    color: #495057;
    font-size: 1rem;
    line-height: 1.7;
}
</style>
