<template>
    <section class="section-spacing bg-white">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-6 section-title">{{ heading }}</h2>
            </div>
            <div class="row g-4">
                <div class="col-md-4" v-for="(reason, index) in reasons" :key="index">
                    <div class="card h-100 text-center shadow-sm card-hover">
                        <div class="card-body p-4">
                            <div class="icon-circle mx-auto mb-3">
                                <i :class="reason.icon" class="fa-lg text-primary"></i>
                            </div>
                            <h5 class="card-title fw-bold">{{ reason.title }}</h5>
                            <p class="card-text text-muted">{{ reason.description }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
import axios from 'axios';
import API_ENDPOINTS from '@/services/api';

const fallbackHeading = 'Почему стоит выбрать именно нас';

const fallbackReasons = [
    {
        title: 'Опытные инструкторы',
        description: 'Сертифицированные специалисты с многолетним стажем. Индивидуальный подход к каждому ученику и психологическая поддержка на всех этапах обучения.',
        icon: 'fas fa-user-graduate',
    },
    {
        title: 'Удобный график',
        description: 'Гибкое расписание практических занятий. Теория проходит в вечернее время — удобно совмещать с работой или учёбой.',
        icon: 'fas fa-clock',
    },
    {
        title: 'Очная форма обучения',
        description: 'Все занятия проходят очно с преподавателем. Вы можете задать вопросы и разобрать сложные ситуации в реальном времени.',
        icon: 'fas fa-chalkboard-teacher',
    },
];

export default {
    name: 'WhyChooseUs',
    data() {
        return {
            heading: fallbackHeading,
            reasons: [...fallbackReasons],
        };
    },
    mounted() {
        axios
            .get(API_ENDPOINTS.whyChooseUs)
            .then((response) => {
                const payload = response.data?.data;
                if (!payload?.blocks?.length) {
                    return;
                }
                this.heading = payload.heading || fallbackHeading;
                this.reasons = payload.blocks.map((b) => ({
                    title: b.title,
                    description: b.description,
                    icon: b.icon,
                }));
            })
            .catch(() => {});
    },
};
</script>

<style scoped>
.icon-circle {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    background: rgba(58, 108, 232, 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
