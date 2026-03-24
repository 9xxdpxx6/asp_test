<template>
    <section class="no-api-section">
        <div class="no-api-services">
            <section v-for="service in services" :key="service.id" class="no-api-service">
                <div class="no-api-service__head">
                    <p class="no-api-service__eyebrow mb-2">{{ service.label }}</p>
                    <h3 class="no-api-service__title mb-0">{{ service.title }}</h3>
                </div>

                <div class="no-api-grid">
                    <article v-for="variant in service.variants" :key="variant.id" class="no-api-card">
                        <div class="no-api-card__top">
                            <span class="no-api-card__tag">{{ variant.tag }}</span>
                            <h4 class="no-api-card__title mb-2">{{ variant.title }}</h4>
                            <p class="no-api-card__text mb-0">{{ variant.description }}</p>
                        </div>

                        <div v-if="variant.mode === 'manual'" class="no-api-manual">
                            <div class="no-api-manual__brand">
                                <span class="no-api-manual__brand-pill" :style="{ background: service.brandColor }">
                                    {{ service.label }}
                                </span>
                            </div>
                            <div class="no-api-manual__stats">
                                <div class="no-api-manual__score">{{ service.rating }}</div>
                                <div class="no-api-manual__meta">
                                    <div class="no-api-manual__stars">★★★★★</div>
                                    <div class="no-api-manual__count">{{ service.countText }}</div>
                                </div>
                            </div>
                            <a
                                :href="service.reviewUrl"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="no-api-manual__link"
                            >
                                Посмотреть и оставить отзыв
                            </a>
                        </div>

                        <div v-else-if="variant.mode === 'qr'" class="no-api-qr">
                            <div class="no-api-qr__content">
                                <div>
                                    <div class="no-api-manual__brand">
                                        <span class="no-api-manual__brand-pill" :style="{ background: service.brandColor }">
                                            {{ service.label }}
                                        </span>
                                    </div>
                                    <div class="no-api-manual__stats no-api-manual__stats--compact">
                                        <div class="no-api-manual__score">{{ service.rating }}</div>
                                        <div class="no-api-manual__meta">
                                            <div class="no-api-manual__stars">★★★★★</div>
                                            <div class="no-api-manual__count">{{ service.countText }}</div>
                                        </div>
                                    </div>
                                    <a
                                        :href="service.reviewUrl"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="no-api-manual__link"
                                    >
                                        Посмотреть и оставить отзыв
                                    </a>
                                </div>

                                <div class="no-api-qr__image-wrap">
                                    <img
                                        v-if="variant.qrCode"
                                        :src="variant.qrCode"
                                        :alt="`QR-код ${service.label}`"
                                        class="no-api-qr__image"
                                    >
                                </div>
                            </div>

                            <div class="no-api-qr__caption">
                                QR ведет на:
                                <a :href="service.addReviewUrl" target="_blank" rel="noopener noreferrer">
                                    форму отзыва
                                </a>
                            </div>
                        </div>

                        <div v-else-if="variant.mode === 'badge-qr'" class="no-api-badge-qr">
                            <div class="no-api-badge-qr__layout">
                                <div class="no-api-badge-qr__badge">
                                    <iframe
                                        :src="service.badgeUrl"
                                        frameborder="0"
                                        loading="lazy"
                                        class="no-api-badge-qr__iframe"
                                        :title="`${service.label} badge`"
                                    ></iframe>
                                </div>

                                <div class="no-api-badge-qr__qr">
                                    <img
                                        v-if="variant.qrCode"
                                        :src="variant.qrCode"
                                        :alt="`QR-код ${service.label}`"
                                        class="no-api-qr__image"
                                    >
                                </div>
                            </div>

                            <div class="no-api-card__actions">
                                <a :href="service.reviewUrl" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-secondary rounded-pill">
                                    Открыть отзывы
                                </a>
                                <a :href="service.addReviewUrl" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-primary rounded-pill">
                                    Оставить отзыв
                                </a>
                            </div>
                        </div>

                        <details class="variant-meta">
                            <summary class="variant-meta__summary">Тех. детали</summary>
                            <div class="variant-meta__body">
                                <div class="variant-meta__line">{{ variant.code }}</div>
                                <div v-if="variant.mode !== 'manual'" class="variant-meta__line">{{ service.addReviewUrl }}</div>
                                <div v-if="variant.mode === 'badge-qr'" class="variant-meta__line">{{ service.badgeUrl }}</div>
                                <div v-if="variant.mode === 'manual'" class="variant-meta__line">{{ service.reviewUrl }}</div>
                            </div>
                        </details>
                    </article>
                </div>
            </section>
        </div>
    </section>
</template>

<script>
import QRCode from 'qrcode';

const createVariants = (serviceId) => ([
    {
        id: `${serviceId}-manual`,
        tag: 'Manual',
        title: 'Ручная карточка',
        description: 'Полностью кастомный блок. Рейтинг и количество отзывов заполняются руками.',
        mode: 'manual',
        code: 'HTML/CSS only + обычная ссылка на отзывы',
    },
    {
        id: `${serviceId}-qr`,
        tag: 'QR',
        title: 'Карточка + QR',
        description: 'Для стойки, ресепшена, распечатки или Telegram. QR код ведет сразу на форму отзыва.',
        mode: 'qr',
        code: 'HTML/CSS + локально сгенерированный QR на add-review URL',
        qrCode: '',
    },
    {
        id: `${serviceId}-badge-qr`,
        tag: 'Hybrid',
        title: 'Официальный badge + QR',
        description: 'Минимум ручной поддержки: официальный badge и рядом QR на страницу отзыва.',
        mode: 'badge-qr',
        code: 'iframe badge + локальный QR + прямые ссылки',
        qrCode: '',
    },
]);

export default {
    name: 'NoApiReviewsDev',
    data() {
        return {
            services: [
                {
                    id: 'yandex',
                    label: 'Яндекс',
                    title: 'Яндекс Карты',
                    rating: '5,0',
                    countText: '55 отзывов • 64 оценки',
                    brandColor: '#4c7af2',
                    reviewUrl: 'https://yandex.ru/maps/org/politekh/179486425425/reviews/',
                    addReviewUrl: 'https://yandex.ru/maps/org/politekh/179486425425/reviews/?add-review',
                    badgeUrl: 'https://yandex.ru/maps-reviews-widget/179486425425',
                    variants: createVariants('yandex'),
                },
                {
                    id: 'twogis',
                    label: '2ГИС',
                    title: '2ГИС',
                    rating: '5,0',
                    countText: '32 отзыва • 36 оценок',
                    brandColor: '#19b24b',
                    reviewUrl: 'https://2gis.ru/krasnodar/branches/70000001046117787/firm/70000001046117788/39.003565%2C45.043388/tab/reviews?m=38.971526%2C45.024767%2F10.79',
                    addReviewUrl: 'https://2gis.ru/krasnodar/branches/70000001046117787/firm/70000001046117788/tab/reviews',
                    badgeUrl: 'https://widget.2gis.ru/api/widget?org_id=70000001046117787&size=small&theme=light',
                    variants: createVariants('twogis'),
                },
            ],
        };
    },
    mounted() {
        this.generateQrCodes();
    },
    methods: {
        async generateQrCodes() {
            for (const service of this.services) {
                for (const variant of service.variants) {
                    if (!['qr', 'badge-qr'].includes(variant.mode)) {
                        continue;
                    }

                    try {
                        variant.qrCode = await QRCode.toDataURL(service.addReviewUrl, {
                            margin: 1,
                            width: 168,
                            color: {
                                dark: '#111827',
                                light: '#FFFFFF',
                            },
                        });
                    } catch (error) {
                        console.error(`Не удалось сгенерировать QR для ${service.id}:`, error);
                    }
                }
            }
        },
    },
};
</script>

<style scoped>
.no-api-section {
    display: flex;
    flex-direction: column;
}

.no-api-services {
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

.no-api-service {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.no-api-service__eyebrow {
    font-size: 0.82rem;
    font-weight: 700;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: #64748b;
}

.no-api-service__title {
    font-size: 1.45rem;
    font-weight: 700;
    color: #0f172a;
}

.no-api-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(260px, 1fr));
    gap: 1.25rem;
}

.no-api-card {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    padding: 1.5rem;
    background: #fff;
    border: 1px solid rgba(15, 23, 42, 0.08);
    border-radius: 24px;
    box-shadow: 0 16px 40px rgba(15, 23, 42, 0.06);
}

.no-api-card__tag {
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

.no-api-card__title {
    font-size: 1.2rem;
    font-weight: 700;
    color: #111827;
}

.no-api-card__text {
    color: #475569;
}

.no-api-manual,
.no-api-qr,
.no-api-badge-qr {
    padding: 1rem;
    border-radius: 18px;
    border: 1px solid rgba(15, 23, 42, 0.08);
    background: #f8fafc;
}

.no-api-manual__brand-pill {
    display: inline-flex;
    align-items: center;
    padding: 0.45rem 0.85rem;
    border-radius: 12px;
    color: #fff;
    font-size: 0.9rem;
    font-weight: 700;
}

.no-api-manual__stats {
    display: flex;
    align-items: center;
    gap: 0.9rem;
    margin: 1rem 0 0.9rem;
}

.no-api-manual__stats--compact {
    margin-top: 0.8rem;
}

.no-api-manual__score {
    font-size: 3rem;
    line-height: 1;
    font-weight: 700;
    color: #111827;
}

.no-api-manual__meta {
    display: flex;
    flex-direction: column;
    gap: 0.2rem;
}

.no-api-manual__stars {
    letter-spacing: 0.12em;
    color: #f5b301;
    font-size: 1rem;
}

.no-api-manual__count {
    color: #475569;
    font-size: 0.92rem;
}

.no-api-manual__link {
    color: #4f46e5;
    font-weight: 600;
    text-decoration: underline;
}

.no-api-qr__content {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 168px;
    gap: 1rem;
    align-items: start;
}

.no-api-qr__image-wrap,
.no-api-badge-qr__qr {
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 0.35rem;
    border-radius: 16px;
    background: #fff;
}

.no-api-qr__image {
    width: 168px;
    height: 168px;
    display: block;
    object-fit: contain;
}

.no-api-qr__caption {
    margin-top: 0.9rem;
    color: #475569;
    font-size: 0.92rem;
}

.no-api-badge-qr__layout {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 168px;
    gap: 1rem;
    align-items: center;
}

.no-api-badge-qr__badge {
    min-height: 120px;
    border-radius: 16px;
    overflow: hidden;
    background: #fff;
    border: 1px solid rgba(15, 23, 42, 0.08);
}

.no-api-badge-qr__iframe {
    width: 100%;
    height: 120px;
    border: 0;
    display: block;
}

.no-api-card__actions {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
    margin-top: 1rem;
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

@media (max-width: 1199px) {
    .no-api-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .no-api-card {
        padding: 1.2rem;
    }

    .no-api-manual__score {
        font-size: 2.5rem;
    }

    .no-api-qr__content,
    .no-api-badge-qr__layout {
        grid-template-columns: 1fr;
    }
}
</style>
