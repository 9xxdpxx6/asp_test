<template>
    <main class="reviews-dev-page section-spacing">
        <div class="container py-5">
            <section class="reviews-dev-hero">
                <p class="reviews-dev-hero__eyebrow mb-2">Dev / Test</p>
                <h1 class="reviews-dev-hero__title mb-3">Витрина виджетов отзывов</h1>
                <p class="reviews-dev-hero__text mb-0">
                    Отдельная тестовая страница со всеми официально доступными вариантами embed-виджетов отзывов
                    для Яндекс.Карт и 2ГИС.
                </p>
            </section>

            <div class="reviews-dev-page__sections">
                <NoApiReviewsDev />
                <YandexReviewsDev />
                <TwoGisReviewsDev />
            </div>
        </div>
    </main>
</template>

<script>
import NoApiReviewsDev from "@/components/dev/NoApiReviewsDev.vue";
import YandexReviewsDev from "@/components/dev/YandexReviewsDev.vue";
import TwoGisReviewsDev from "@/components/dev/TwoGisReviewsDev.vue";

export default {
    name: 'DevReviewsWidgets',
    components: {
        NoApiReviewsDev,
        YandexReviewsDev,
        TwoGisReviewsDev,
    },
};
</script>

<style scoped>
.reviews-dev-page {
    min-height: 100%;
    background:
        radial-gradient(circle at top left, rgba(14, 165, 233, 0.12), transparent 28%),
        radial-gradient(circle at top right, rgba(34, 197, 94, 0.12), transparent 32%),
        linear-gradient(180deg, #f8fafc 0%, #eef2f7 100%);
}

.reviews-dev-hero {
    margin-bottom: 2.5rem;
    padding: 2rem;
    border: 1px solid rgba(15, 23, 42, 0.08);
    border-radius: 28px;
    background: rgba(255, 255, 255, 0.9);
    box-shadow: 0 24px 60px rgba(15, 23, 42, 0.08);
}

.reviews-dev-hero__eyebrow {
    font-size: 0.82rem;
    font-weight: 700;
    letter-spacing: 0.14em;
    text-transform: uppercase;
    color: #64748b;
}

.reviews-dev-hero__title {
    font-size: clamp(2rem, 4vw, 3.2rem);
    font-weight: 800;
    color: #0f172a;
}

.reviews-dev-hero__text {
    max-width: 760px;
    font-size: 1.05rem;
    color: #475569;
}

.reviews-dev-page__sections {
    display: flex;
    flex-direction: column;
    gap: 2.5rem;
}

@media (max-width: 768px) {
    .reviews-dev-hero {
        padding: 1.25rem;
        border-radius: 22px;
    }
}
</style>
