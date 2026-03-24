<template>
    <section class="hero-section" :style="{ backgroundImage: `url(${backgroundImage})` }">
        <div class="hero-overlay">
            <div class="container text-center text-white">
                <h1 class="display-4 fw-bold mb-3">{{ title }}</h1>
                <p class="lead mb-4 mx-auto hero-lead" v-if="subtitle">{{ subtitle }}</p>
                <div
                    class="hero-cta-toolbar d-flex gap-3 w-100"
                    :class="[flexButtonClasses, { 'hero-cta-toolbar--equal-slots': useEqualRowSlots }]"
                >
                    <div v-for="(btn, idx) in ctaButtons" :key="idx" class="hero-cta-slot">
                        <HeroCtaButton
                            :variant="btn.variant"
                            :label="btn.label"
                            :icon="btn.icon"
                            :href="btn.href"
                        />
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
import HeroCtaButton from './HeroCtaButton.vue';

const FALLBACK = {
    background_image_url: '/images/slider/slide-1-cars.JPG',
    title: 'Автошкола Политех',
    subtitle: 'Обучение категорий A и B в Краснодаре. Опытные инструкторы, современные автомобили, удобное расписание.',
    buttons_align: 'center',
    buttons_direction: 'row',
    cta_buttons: [
        { label: 'Узнать стоимость', icon: 'fas fa-graduation-cap', href: '/prices', variant: 'light' },
        { label: 'Записаться', icon: 'fas fa-phone', href: '#pricing-preview', variant: 'outline-light' },
    ],
};

export default {
    name: 'HeroSection',
    components: { HeroCtaButton },
    props: {
        settings: {
            type: Object,
            default: null,
        },
    },
    computed: {
        cfg() {
            return this.settings && typeof this.settings === 'object' ? { ...FALLBACK, ...this.settings } : FALLBACK;
        },
        backgroundImage() {
            return this.cfg.background_image_url || FALLBACK.background_image_url;
        },
        title() {
            return this.cfg.title || FALLBACK.title;
        },
        subtitle() {
            return this.cfg.subtitle ?? FALLBACK.subtitle;
        },
        ctaButtons() {
            const raw = this.cfg.cta_buttons;
            if (Array.isArray(raw) && raw.length > 0) {
                return raw.map((b, i) => ({
                    label: b.label || '',
                    icon: b.icon || 'fas fa-check',
                    href: b.href || '/',
                    variant: b.variant === 'outline-light' || b.variant === 'light' ? b.variant : (i % 2 === 0 ? 'light' : 'outline-light'),
                }));
            }
            return FALLBACK.cta_buttons;
        },
        alignNorm() {
            const a = this.cfg.buttons_align || 'center';
            return a === 'around' ? 'between' : a;
        },
        /** Равные колонки только для «с промежутком» в ряду — иначе работает justify-content. */
        useEqualRowSlots() {
            return this.cfg.buttons_direction !== 'column' && this.alignNorm === 'between';
        },
        /**
         * В ряду: выравнивание по главной оси (горизонталь).
         * В колонке: слева/центр/справа — поперечная ось (align-items).
         */
        flexButtonClasses() {
            const col = this.cfg.buttons_direction === 'column';
            const alignNorm = this.alignNorm;

            const justifyRow = {
                center: 'justify-content-center',
                start: 'justify-content-start',
                end: 'justify-content-end',
                between: 'justify-content-between',
            };

            const alignCross = {
                center: 'align-items-center',
                start: 'align-items-start',
                end: 'align-items-end',
                between: 'align-items-center',
            };

            if (!col) {
                return ['flex-row', 'flex-wrap', 'align-items-stretch', justifyRow[alignNorm] || justifyRow.center];
            }

            if (alignNorm === 'between') {
                return ['flex-column', alignCross.between, 'justify-content-between'];
            }

            return ['flex-column', alignCross[alignNorm] || 'align-items-center', 'justify-content-start'];
        },
    },
};
</script>

<style scoped>
.hero-section {
    min-height: 550px;
    background-size: cover;
    background-position: center;
    position: relative;
    display: flex;
    align-items: center;
    margin-top: -10px;
}

.hero-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(
        to bottom,
        rgba(0, 0, 0, 0.45),
        rgba(0, 0, 0, 0.6)
    );
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem 1rem;
}

.hero-section h1 {
    text-shadow: 0 2px 8px rgba(0, 0, 0, 0.4);
}

.hero-lead {
    text-shadow: 0 1px 4px rgba(0, 0, 0, 0.3);
    font-size: 1.15rem;
    max-width: 600px;
}

/* Равные «слоты» только при «с промежутком»: одинаковая ширина колонок — короткая подпись не смещает соседей */
.hero-cta-toolbar.flex-row.hero-cta-toolbar--equal-slots .hero-cta-slot {
    flex: 1 1 0;
    min-width: min(100%, 10rem);
    max-width: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
}

.hero-cta-toolbar.flex-row:not(.hero-cta-toolbar--equal-slots) .hero-cta-slot {
    flex: 0 1 auto;
    min-width: 0;
    display: flex;
    justify-content: center;
    align-items: center;
}

.hero-cta-toolbar.flex-row {
    max-width: 960px;
    margin-left: auto;
    margin-right: auto;
}

.hero-cta-toolbar.flex-column .hero-cta-slot {
    width: 100%;
    max-width: 24rem;
    display: flex;
    justify-content: center;
    align-items: center;
}

.hero-cta-slot :deep(a.btn) {
    max-width: 100%;
    white-space: normal;
    text-align: center;
    word-break: break-word;
}

@media (max-width: 768px) {
    .hero-section {
        min-height: 400px;
    }
}
</style>
