<template>
    <section class="reviews-dev-section">
        <div class="reviews-dev-section__head">
            <p class="reviews-dev-section__eyebrow mb-2">2GIS</p>
            <h2 class="reviews-dev-section__title mb-2">Встраиваемые варианты 2ГИС</h2>
            <p class="reviews-dev-section__text mb-0">
                Здесь оставлены только те варианты 2ГИС, которые реально работают как embed на странице:
                <code>small</code> и <code>medium</code> в темах <code>light</code> и <code>dark</code>.
            </p>
        </div>

        <div class="reviews-dev-grid">
            <article
                v-for="variant in embedVariants"
                :key="variant.id"
                class="reviews-dev-card"
            >
                <div class="reviews-dev-card__top">
                    <div class="reviews-dev-card__tags">
                        <span class="reviews-dev-card__tag">{{ variant.size }}</span>
                        <span
                            :class="[
                                'reviews-dev-card__tag',
                                variant.theme === 'dark' ? 'reviews-dev-card__tag--dark' : 'reviews-dev-card__tag--light'
                            ]"
                        >
                            {{ variant.theme }}
                        </span>
                    </div>
                    <h3 class="reviews-dev-card__title mb-2">{{ variant.title }}</h3>
                    <p class="reviews-dev-card__text mb-0">{{ variant.description }}</p>
                </div>

                <div
                    :class="[
                        'reviews-dev-card__frame',
                        variant.theme === 'dark' ? 'reviews-dev-card__frame--dark' : 'reviews-dev-card__frame--light'
                    ]"
                    :style="{ height: `${variant.height}px` }"
                >
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
                    <a :href="reviewsUrl" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-success rounded-pill">
                        Отзывы в 2ГИС
                    </a>
                </div>
            </article>
        </div>
    </section>
</template>

<script>
const ORG_ID = '70000001046117787';
const REVIEWS_URL = 'https://2gis.ru/krasnodar/branches/70000001046117787/firm/70000001046117788/39.003565%2C45.043388/tab/reviews?m=38.971526%2C45.024767%2F10.79';

const buildVariant = (size, theme, height, description) => ({
    id: `${size}-${theme}`,
    size,
    theme,
    title: `${size} / ${theme}`,
    description,
    height,
    src: `https://widget.2gis.ru/api/widget?org_id=${ORG_ID}&size=${size}&theme=${theme}`,
});

export default {
    name: 'TwoGisReviewsDev',
    data() {
        return {
            reviewsUrl: REVIEWS_URL,
            embedVariants: [
                buildVariant('small', 'light', 64, 'Минимальный компактный рейтинг-виджет.'),
                buildVariant('small', 'dark', 64, 'Тот же компактный виджет в тёмной теме.'),
                buildVariant('medium', 'light', 84, 'Средний формат рейтинга без развёрнутой ленты.'),
                buildVariant('medium', 'dark', 84, 'Средний рейтинг-виджет в тёмной теме.'),
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

.reviews-dev-card__tags {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.reviews-dev-card__tag {
    display: inline-flex;
    align-self: flex-start;
    padding: 0.3rem 0.7rem;
    border-radius: 999px;
    background: #ecfeff;
    color: #155e75;
    font-size: 0.78rem;
    font-weight: 700;
    letter-spacing: 0.04em;
    text-transform: uppercase;
}

.reviews-dev-card__tag--light {
    background: #f1f5f9;
    color: #334155;
}

.reviews-dev-card__tag--dark {
    background: #111827;
    color: #f8fafc;
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
    border-radius: 18px;
    border: 1px solid rgba(15, 23, 42, 0.08);
}

.reviews-dev-card__frame--light {
    background: #f8fafc;
}

.reviews-dev-card__frame--dark {
    background: #0f172a;
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
}
</style>
