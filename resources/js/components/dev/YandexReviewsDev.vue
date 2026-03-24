<template>
    <section class="reviews-dev-section">
        <div class="reviews-dev-section__head">
            <p class="reviews-dev-section__eyebrow mb-2">Yandex</p>
            <h2 class="reviews-dev-section__title mb-2">Официальные варианты Яндекс.Карт</h2>
            <p class="reviews-dev-section__text mb-0">
                У Яндекса доступны два embed-режима: компактный badge и лента отзывов с параметром
                <code>?comments</code>.
            </p>
        </div>

        <div class="reviews-dev-grid">
            <article
                v-for="variant in variants"
                :key="variant.id"
                :class="['reviews-dev-card', { 'reviews-dev-card--wide': variant.layout === 'wide' }]"
            >
                <div class="reviews-dev-card__top">
                    <span class="reviews-dev-card__tag">{{ variant.badge }}</span>
                    <h3 class="reviews-dev-card__title mb-2">{{ variant.title }}</h3>
                    <p class="reviews-dev-card__text mb-0">{{ variant.description }}</p>
                </div>

                <div class="reviews-dev-card__frame" :style="{ height: `${variant.height}px` }">
                    <iframe
                        :src="variant.src"
                        class="reviews-dev-card__iframe"
                        frameborder="0"
                        loading="lazy"
                        :title="variant.title"
                    ></iframe>
                </div>

                <details class="variant-meta">
                    <summary class="variant-meta__summary">Тех. детали</summary>
                    <div class="variant-meta__body">
                        <div class="variant-meta__line">{{ variant.src }}</div>
                    </div>
                </details>

                <div class="reviews-dev-card__actions">
                    <a :href="variant.src" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-secondary rounded-pill">
                        Открыть iframe
                    </a>
                    <a :href="mapsReviewsUrl" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-dark rounded-pill">
                        Отзывы в Яндексе
                    </a>
                </div>
            </article>
        </div>
    </section>
</template>

<script>
const YANDEX_ORG_ID = '179486425425';
const YANDEX_MAPS_REVIEWS_URL = `https://yandex.ru/maps/org/politekh/${YANDEX_ORG_ID}/reviews/`;

export default {
    name: 'YandexReviewsDev',
    data() {
        return {
            mapsReviewsUrl: YANDEX_MAPS_REVIEWS_URL,
            variants: [
                {
                    id: 'badge',
                    badge: 'Badge',
                    title: 'Бейдж рейтинга',
                    description: 'Компактная карточка рейтинга без ленты отзывов.',
                    src: `https://yandex.ru/maps-reviews-widget/${YANDEX_ORG_ID}`,
                    height: 120,
                    layout: 'default',
                },
                {
                    id: 'comments',
                    badge: 'Comments',
                    title: 'Лента отзывов',
                    description: 'Полный официальный виджет с отзывами. Включается параметром ?comments.',
                    src: `https://yandex.ru/maps-reviews-widget/${YANDEX_ORG_ID}?comments`,
                    height: 760,
                    layout: 'wide',
                },
            ],
        };
    },
};
</script>

<style scoped>
.reviews-dev-section {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.reviews-dev-section__eyebrow {
    font-size: 0.82rem;
    font-weight: 700;
    letter-spacing: 0.14em;
    text-transform: uppercase;
    color: #64748b;
}

.reviews-dev-section__title {
    font-size: 1.8rem;
    font-weight: 700;
    color: #0f172a;
}

.reviews-dev-section__text {
    max-width: 760px;
    color: #475569;
}

.reviews-dev-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(280px, 1fr));
    gap: 1.5rem;
}

.reviews-dev-card {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    padding: 1.5rem;
    background: #fff;
    border: 1px solid rgba(15, 23, 42, 0.08);
    border-radius: 24px;
    box-shadow: 0 16px 40px rgba(15, 23, 42, 0.06);
}

.reviews-dev-card--wide {
    grid-column: span 2;
}

.reviews-dev-card__tag {
    display: inline-flex;
    align-self: flex-start;
    padding: 0.3rem 0.7rem;
    border-radius: 999px;
    background: #eef2ff;
    color: #3730a3;
    font-size: 0.78rem;
    font-weight: 700;
    letter-spacing: 0.04em;
    text-transform: uppercase;
}

.reviews-dev-card__title {
    font-size: 1.3rem;
    font-weight: 700;
    color: #111827;
}

.reviews-dev-card__text {
    color: #475569;
}

.reviews-dev-card__frame {
    overflow: hidden;
    border: 1px solid rgba(15, 23, 42, 0.08);
    border-radius: 18px;
    background: #f8fafc;
}

.reviews-dev-card__iframe {
    width: 100%;
    height: 100%;
    border: 0;
    display: block;
}

.variant-meta {
    margin-top: 0.15rem;
}

.variant-meta__summary {
    display: inline-flex;
    align-items: center;
    color: #94a3b8;
    font-size: 0.78rem;
    cursor: pointer;
    user-select: none;
}

.variant-meta__summary::marker,
.variant-meta__summary::-webkit-details-marker {
    color: #cbd5e1;
}

.variant-meta[open] .variant-meta__summary {
    margin-bottom: 0.55rem;
}

.variant-meta__body {
    display: flex;
    flex-direction: column;
    gap: 0.45rem;
}

.variant-meta__line {
    padding: 0.7rem 0.85rem;
    background: #f8fafc;
    border: 1px solid rgba(15, 23, 42, 0.06);
    border-radius: 12px;
    color: #64748b;
    font-size: 0.76rem;
    line-height: 1.45;
    word-break: break-word;
}

.reviews-dev-card__actions {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
}

@media (max-width: 991px) {
    .reviews-dev-grid {
        grid-template-columns: 1fr;
    }

    .reviews-dev-card--wide {
        grid-column: auto;
    }
}
</style>
