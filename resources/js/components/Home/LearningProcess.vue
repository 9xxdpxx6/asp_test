<template>
    <section class="section-spacing">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-6 section-title">{{ heading }}</h2>
                <p class="text-muted mt-3">{{ subheading }}</p>
            </div>

            <div class="steps-row row g-4">
                <div
                    class="col-sm-6 col-lg-3 step-col"
                    :class="{ 'step-col--last': index === steps.length - 1 }"
                    v-for="(step, index) in steps"
                    :key="index"
                >
                    <div class="text-center step-content">
                        <div class="step-number mx-auto mb-3">
                            <span>{{ index + 1 }}</span>
                        </div>
                        <div class="step-icon mx-auto mb-3">
                            <i :class="step.icon" class="fa-2x text-primary"></i>
                        </div>
                        <h5 class="fw-bold">{{ step.title }}</h5>
                        <p class="text-muted small mb-0">{{ step.description }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
import axios from 'axios';
import API_ENDPOINTS from '@/services/api';

const fallbackHeading = 'Как проходит обучение';
const fallbackSubheading = 'Четыре простых шага от записи до получения прав';

const fallbackSteps = [
    {
        icon: 'fas fa-file-signature',
        title: 'Запись',
        description: 'Оставьте заявку на сайте или позвоните нам. Мы подберём удобное расписание.',
    },
    {
        icon: 'fas fa-book-open',
        title: 'Теория',
        description: 'Изучайте ПДД с опытными преподавателями в удобном формате.',
    },
    {
        icon: 'fas fa-car',
        title: 'Практика',
        description: 'Уроки вождения с персональным инструктором на современных автомобилях.',
    },
    {
        icon: 'fas fa-trophy',
        title: 'Экзамен',
        description: 'Подготовка к экзамену в ГИБДД и сопровождение до получения прав.',
    },
];

export default {
    name: 'LearningProcess',
    data() {
        return {
            heading: fallbackHeading,
            subheading: fallbackSubheading,
            steps: [...fallbackSteps],
        };
    },
    mounted() {
        axios
            .get(API_ENDPOINTS.learningProcess)
            .then((response) => {
                const payload = response.data?.data;
                if (!payload?.blocks?.length) {
                    return;
                }
                this.heading = payload.heading || fallbackHeading;
                this.subheading = payload.subheading || fallbackSubheading;
                this.steps = payload.blocks.map((b) => ({
                    icon: b.icon,
                    title: b.title,
                    description: b.description,
                }));
            })
            .catch(() => {});
    },
};
</script>

<style scoped>
.step-number {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: var(--bs-primary);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 1rem;
}

.step-icon {
    width: 72px;
    height: 72px;
    border-radius: 50%;
    background: rgba(58, 108, 232, 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
}

/* ---- Desktop connector lines (lg+) ---- */
@media (min-width: 992px) {
    .step-col {
        position: relative;
    }

    .step-col:not(.step-col--last)::after {
        content: '';
        position: absolute;
        top: 54px;
        left: calc(50% + 48px);
        width: calc(100% - 96px);
        border-top: 2px dashed var(--bs-primary);
        opacity: 0.35;
    }
}

/* ---- Mobile connector lines (< lg) ---- */
@media (max-width: 991.98px) {
    .step-col:not(.step-col--last) .step-content::after {
        content: '';
        display: block;
        width: 2px;
        height: 28px;
        margin: 0.75rem auto 0;
        background: var(--bs-primary);
        opacity: 0.25;
        border-radius: 1px;
    }
}
</style>
