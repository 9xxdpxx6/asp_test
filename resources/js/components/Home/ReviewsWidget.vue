<template>
    <section v-if="widgets.length" class="section-spacing bg-white">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-6 section-title">Отзывы наших учеников</h2>
            </div>

            <div
                class="reviews-widget__grid"
                :class="{ 'reviews-widget__grid--single': widgets.length === 1 }"
            >
                <article
                    v-for="widget in widgets"
                    :key="widget.id ?? widget.slug"
                    :class="['reviews-widget__card', { 'reviews-widget__card--compact': isCompactWidget(widget) }]"
                >
                    <div
                        v-if="widget.render_type === 'iframe_src'"
                        class="reviews-widget__frame-wrap"
                        :class="{ 'reviews-widget__frame-wrap--compact': isCompactWidget(widget) }"
                    >
                        <iframe
                            :src="widget.config?.src"
                            frameborder="0"
                            loading="lazy"
                            class="reviews-widget__frame"
                            :style="iframeStyle(widget.config?.height)"
                            :title="widget.title"
                        ></iframe>
                    </div>

                    <div
                        v-else-if="widget.render_type === '2gis_constructor'"
                        class="reviews-widget__frame-wrap"
                    >
                        <iframe
                            :ref="(el) => setConstructorFrameRef(widget.id ?? widget.slug, el)"
                            frameborder="0"
                            sandbox="allow-modals allow-forms allow-scripts allow-same-origin allow-popups allow-top-navigation-by-user-activation"
                            class="reviews-widget__frame"
                            :style="iframeStyle(widget.config?.height)"
                            :title="widget.title"
                        ></iframe>
                    </div>

                    <div
                        v-else-if="widget.render_type === 'manual'"
                        class="reviews-widget__manual"
                        :class="`reviews-widget__manual--${widget.provider}`"
                    >
                        <div class="reviews-widget__manual-mark">
                            {{ widget.provider === 'yandex' ? 'Я' : '2ГИС' }}
                        </div>
                        <div>
                            <div class="reviews-widget__rating-row">
                                <span class="reviews-widget__rating-value">{{ ratingValue(widget) }}</span>
                                <span class="reviews-widget__rating-stars">★★★★★</span>
                            </div>
                        </div>
                    </div>

                    <div
                        v-else-if="widget.render_type === 'qr'"
                        class="reviews-widget__compact-layout reviews-widget__compact-layout--qr"
                    >
                        <div class="reviews-widget__compact-panel">
                            <div class="reviews-widget__compact-service">
                                <span :class="['reviews-widget__compact-mark', `reviews-widget__compact-mark--${widget.provider}`]">
                                    {{ widget.provider === 'yandex' ? 'Я' : '2ГИС' }}
                                </span>
                                <span class="reviews-widget__compact-label">{{ widget.config?.service_label || providerLabel(widget.provider) }}</span>
                            </div>
                            <div class="reviews-widget__compact-rating">
                                <span class="reviews-widget__compact-value">{{ ratingValue(widget) }}</span>
                                <span class="reviews-widget__compact-stars">★★★★★</span>
                            </div>
                            <div class="reviews-widget__compact-count">{{ reviewCountText(widget) }}</div>
                        </div>

                        <div v-if="qrCodes[widget.id ?? widget.slug]" class="reviews-widget__qr-box reviews-widget__qr-box--compact">
                            <img
                                :src="qrCodes[widget.id ?? widget.slug]"
                                class="reviews-widget__qr-image"
                                :alt="`QR-код для ${widget.config?.service_label || providerLabel(widget.provider)}`"
                            />
                        </div>
                    </div>

                    <div
                        v-else-if="widget.render_type === 'badge_qr'"
                        class="reviews-widget__compact-layout reviews-widget__compact-layout--badge"
                    >
                        <div class="reviews-widget__compact-panel reviews-widget__compact-panel--badge">
                            <div class="reviews-widget__compact-badge-inner">
                                <div class="reviews-widget__compact-badge-content">
                                    <div class="reviews-widget__compact-service">
                                        <span :class="['reviews-widget__compact-mark', `reviews-widget__compact-mark--${widget.provider}`]">
                                            {{ widget.provider === 'yandex' ? 'Я' : '2ГИС' }}
                                        </span>
                                        <span class="reviews-widget__compact-label">{{ widget.config?.service_label || providerLabel(widget.provider) }}</span>
                                    </div>
                                    <div class="reviews-widget__compact-rating">
                                        <span class="reviews-widget__compact-value">{{ ratingValue(widget) }}</span>
                                        <span class="reviews-widget__compact-stars">★★★★★</span>
                                    </div>
                                    <div class="reviews-widget__compact-count">{{ reviewCountText(widget) }}</div>
                                    <div class="reviews-widget__compact-link">{{ compactLinkText(widget) }}</div>
                                </div>
                                <div v-if="qrCodes[widget.id ?? widget.slug]" class="reviews-widget__compact-qr-wrap">
                                    <img
                                        :src="qrCodes[widget.id ?? widget.slug]"
                                        class="reviews-widget__qr-image"
                                        :alt="`QR-код для ${widget.config?.service_label || providerLabel(widget.provider)}`"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    <a
                        v-if="widget.config?.button_url"
                        :href="widget.config.button_url"
                        target="_blank"
                        rel="noopener noreferrer"
                        :class="buttonClass()"
                    >
                        {{ widget.config?.button_text || 'Оставить отзыв' }}
                    </a>
                </article>
            </div>
        </div>
    </section>
</template>

<script>
import axios from 'axios';
import QRCode from 'qrcode';
import API_ENDPOINTS from '@/services/api';

const FALLBACK_WIDGETS = [
    {
        id: 'fallback-2gis',
        slug: 'twogis_constructor_big',
        title: '2ГИС: большой конструктор',
        description: 'Текущий большой виджет 2ГИС, который уже используется на главной.',
        provider: '2gis',
        render_type: '2gis_constructor',
        config: {
            service_label: '2ГИС',
            card_title: 'Отзывы в 2ГИС',
            card_text: 'Большой фирменный виджет с лентой отзывов и прямой кнопкой в карточку организации.',
            size: 'big',
            theme: 'light',
            org_id: '70000001046117787',
            branch_id: '',
            height: 824,
            button_text: 'Оставить отзыв',
            button_url: 'https://2gis.ru/krasnodar/branches/70000001046117787/firm/70000001046117788/39.003565%2C45.043388/tab/reviews?m=38.971526%2C45.024767%2F10.79&addReview',
            button_variant: 'primary',
        },
    },
    {
        id: 'fallback-yandex',
        slug: 'yandex_comments',
        title: 'Яндекс: лента отзывов',
        description: 'Официальный iframe-виджет Яндекс Карт с комментариями.',
        provider: 'yandex',
        render_type: 'iframe_src',
        config: {
            service_label: 'Яндекс Карты',
            card_title: 'Отзывы на Яндексе',
            card_text: 'Официальный виджет Яндекс Карт с отзывами и кнопкой перехода на форму добавления.',
            src: 'https://yandex.ru/maps-reviews-widget/179486425425?comments',
            height: 824,
            button_text: 'Оставить отзыв',
            button_url: 'https://yandex.ru/maps/org/politekh/179486425425/reviews/?add-review',
            button_variant: 'primary',
        },
    },
];

export default {
    name: 'ReviewsWidget',
    data() {
        return {
            widgets: FALLBACK_WIDGETS,
            qrCodes: {},
            constructorFrameRefs: {},
        };
    },
    mounted() {
        this.refreshRenderedWidgets();
        this.fetchWidgets();
    },
    methods: {
        fetchWidgets() {
            axios.get(API_ENDPOINTS.reviewWidgetsHome)
                .then(({ data }) => {
                    const items = Array.isArray(data?.data) ? data.data : [];
                    this.widgets = items.length ? items : FALLBACK_WIDGETS;
                    this.refreshRenderedWidgets();
                })
                .catch(() => {
                    this.widgets = FALLBACK_WIDGETS;
                    this.refreshRenderedWidgets();
                });
        },
        refreshRenderedWidgets() {
            this.$nextTick(() => {
                this.mountConstructorWidgets();
                this.generateQrCodes();
            });
        },
        setConstructorFrameRef(key, element) {
            if (!key) {
                return;
            }

            if (element) {
                this.constructorFrameRefs[key] = element;
                return;
            }

            delete this.constructorFrameRefs[key];
        },
        mountConstructorWidgets() {
            this.widgets
                .filter((widget) => widget.render_type === '2gis_constructor')
                .forEach((widget) => {
                    const key = widget.id ?? widget.slug;
                    const frame = this.constructorFrameRefs[key];

                    if (!frame || !frame.contentWindow || !frame.contentWindow.document) {
                        return;
                    }

                    const marker = widget.slug || String(key);
                    if (frame.dataset.loaded === marker) {
                        return;
                    }

                    try {
                        const frameDocument = frame.contentWindow.document;
                        frameDocument.open();
                        frameDocument.write(this.getTwoGisConstructorHtml(widget.config || {}));
                        frameDocument.close();
                        frame.dataset.loaded = marker;
                    } catch (error) {
                        console.error('Не удалось смонтировать 2ГИС виджет:', error);
                    }
                });
        },
        async generateQrCodes() {
            const candidates = this.widgets.filter((widget) => ['qr', 'badge_qr'].includes(widget.render_type));

            for (const widget of candidates) {
                const key = widget.id ?? widget.slug;
                const qrUrl = widget.config?.qr_url || widget.config?.button_url;

                if (!key || !qrUrl || this.qrCodes[key]) {
                    continue;
                }

                try {
                    const dataUrl = await QRCode.toDataURL(qrUrl, {
                        width: 180,
                        margin: 1,
                        color: {
                            dark: '#111827',
                            light: '#ffffff',
                        },
                    });

                    this.qrCodes = {
                        ...this.qrCodes,
                        [key]: dataUrl,
                    };
                } catch (error) {
                    console.error('Не удалось сгенерировать QR-код:', error);
                }
            }
        },
        getTwoGisConstructorHtml(config) {
            const size = config.size || 'big';
            const theme = config.theme || 'light';
            const branchId = config.branch_id || '';
            const orgId = config.org_id || '70000001046117787';

            return `
                <head>
                    <meta charset="UTF-8">
                    <style>html,body{margin:0;padding:0;background:#fff;}#iframe{min-height:100vh;}</style>
                    <script type="text/javascript">
                        window.__size__=${JSON.stringify(size)};
                        window.__theme__=${JSON.stringify(theme)};
                        window.__branchId__=${JSON.stringify(branchId)};
                        window.__orgId__=${JSON.stringify(orgId)};
                    <\/script>
                    <script crossorigin="anonymous" type="module" src="https://disk.2gis.com/widget-constructor/assets/iframe.js"><\/script>
                    <link rel="modulepreload" crossorigin="anonymous" href="https://disk.2gis.com/widget-constructor/assets/defaults.js">
                    <link rel="stylesheet" crossorigin="anonymous" href="https://disk.2gis.com/widget-constructor/assets/defaults.css">
                </head>
                <body><div id="iframe"></div></body>
            `;
        },
        providerLabel(provider) {
            return provider === 'yandex' ? 'Яндекс Карты' : '2ГИС';
        },
        ratingValue(widget) {
            if (widget?.config?.rating_value) {
                return widget.config.rating_value;
            }

            return widget?.provider === 'yandex' ? '5.0' : '5.0';
        },
        reviewCountText(widget) {
            if (widget?.config?.review_count_text) {
                return widget.config.review_count_text;
            }

            return widget?.provider === 'yandex' ? '55 отзывов' : '32 отзыва';
        },
        compactLinkText(widget) {
            if (widget?.config?.compact_link_text) {
                return widget.config.compact_link_text;
            }

            return widget?.provider === 'yandex'
                ? 'Посмотреть и оставить отзыв на Яндекс Картах'
                : 'Посмотреть и оставить отзыв в 2ГИС';
        },
        isCompactWidget(widget) {
            if (['manual', 'qr', 'badge_qr'].includes(widget.render_type)) {
                return true;
            }

            return Number(widget.config?.height || 0) <= 260;
        },
        iframeStyle(height) {
            const resolvedHeight = Number(height || 420);
            return {
                height: `${resolvedHeight}px`,
            };
        },
        buttonClass() {
            return 'btn btn-lg rounded-pill px-4 reviews-widget__action btn-primary';
        },
    },
};
</script>

<style scoped>
.reviews-widget__grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 1.75rem;
    align-items: start;
}

/* Один виджет — на всю ширину контейнера по центру секции; два и больше — две колонки (на широких экранах). */
.reviews-widget__grid--single {
    grid-template-columns: 1fr;
}

.reviews-widget__card {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    min-height: 100%;
}

.reviews-widget__card--compact {
    border: 1px solid #e8edf5;
    border-radius: 24px;
    padding: 1.25rem;
    box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
    min-height: 100%;
}

.reviews-widget__frame-wrap {
    width: 100%;
    background: #fff;
    border-radius: 18px;
    overflow: hidden;
    box-shadow: 0 12px 34px rgba(15, 23, 42, 0.1);
}

.reviews-widget__frame-wrap--compact {
    box-shadow: none;
    border: 1px solid #e7ecf3;
}

.reviews-widget__frame-wrap--badge {
    flex: 1 1 230px;
}

.reviews-widget__frame {
    width: 100%;
    display: block;
    border: 0;
}

.reviews-widget__manual {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    min-height: 180px;
    padding: 1.1rem;
    border-radius: 20px;
    background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
    border: 1px solid #e7ecf3;
}

.reviews-widget__manual--yandex .reviews-widget__manual-mark {
    background: #ffd53d;
    color: #212121;
}

.reviews-widget__manual--2gis .reviews-widget__manual-mark {
    background: #15a34a;
    color: #ffffff;
}

.reviews-widget__manual-mark {
    width: 56px;
    height: 56px;
    flex: 0 0 56px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    font-weight: 800;
    letter-spacing: 0.04em;
}

.reviews-widget__rating-row {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.55rem;
}

.reviews-widget__rating-value {
    font-size: 1.1rem;
    font-weight: 800;
    color: #132238;
    line-height: 1;
}

.reviews-widget__rating-stars {
    color: #f5b400;
    font-size: 0.9rem;
    letter-spacing: 0.08em;
    line-height: 1;
}

.reviews-widget__compact-layout {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 170px;
    gap: 1rem;
    min-height: 214px;
    padding: 1rem;
    border: 1px solid #e7ecf3;
    border-radius: 24px;
    background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
    align-items: stretch;
}

.reviews-widget__compact-panel {
    display: flex;
    flex-direction: column;
    justify-content: center;
    min-height: 100%;
    padding: 1rem;
    border-radius: 20px;
    background: #ffffff;
}

.reviews-widget__compact-panel--badge {
    background: #f7f4ee;
}

.reviews-widget__compact-layout--badge {
    display: block;
    padding: 0;
    border: 0;
    background: transparent;
    min-height: 0;
    gap: 0;
}

.reviews-widget__compact-badge-inner {
    display: flex;
    align-items: stretch;
    gap: 1.25rem;
}

.reviews-widget__compact-badge-content {
    flex: 1;
    min-width: 0;
}

.reviews-widget__compact-qr-wrap {
    flex: 0 0 auto;
    display: flex;
    align-items: center;
    justify-content: center;
    align-self: center;
    padding: 0.15rem;
}

.reviews-widget__compact-service {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 0.9rem;
}

.reviews-widget__compact-mark {
    width: 44px;
    height: 44px;
    flex: 0 0 44px;
    border-radius: 14px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.9rem;
    font-weight: 800;
}

.reviews-widget__compact-mark--yandex {
    background: #ffd53d;
    color: #212121;
}

.reviews-widget__compact-mark--2gis {
    background: #15a34a;
    color: #ffffff;
}

.reviews-widget__compact-label {
    color: #7b8798;
    font-size: 0.8rem;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}

.reviews-widget__compact-title {
    color: #132238;
    font-size: 1.15rem;
    font-weight: 700;
    line-height: 1.3;
    margin-bottom: 0.8rem;
}

.reviews-widget__compact-rating {
    display: flex;
    align-items: center;
    gap: 0.55rem;
    margin-bottom: 0.45rem;
}

.reviews-widget__compact-value {
    color: #132238;
    font-size: 2.1rem;
    font-weight: 800;
    line-height: 1;
}

.reviews-widget__compact-stars {
    color: #f5b400;
    font-size: 1rem;
    letter-spacing: 0.08em;
    line-height: 1;
}

.reviews-widget__compact-count {
    color: #7b8798;
    font-size: 0.92rem;
    line-height: 1.4;
}

.reviews-widget__compact-link {
    margin-top: 0.8rem;
    color: #4c63d2;
    font-weight: 600;
    line-height: 1.5;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.reviews-widget__qr-box {
    flex: 0 0 160px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0.8rem;
    border-radius: 20px;
    border: 1px solid #e7ecf3;
    background: #ffffff;
}

.reviews-widget__qr-box--compact {
    flex-basis: auto;
    min-height: 100%;
}

.reviews-widget__qr-image {
    width: 140px;
    height: 140px;
    object-fit: contain;
}

.reviews-widget__action {
    align-self: flex-start;
}

@media (max-width: 991px) {
    .reviews-widget__grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .reviews-widget__qr-layout {
        flex-direction: column;
    }

    .reviews-widget__qr-box {
        flex-basis: auto;
    }

    .reviews-widget__compact-layout {
        grid-template-columns: 1fr;
    }

    .reviews-widget__compact-badge-inner {
        flex-direction: column;
        align-items: center;
    }
}
</style>
