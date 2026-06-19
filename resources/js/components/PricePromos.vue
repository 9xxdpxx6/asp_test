<template>
    <div class="price-promos" :class="orientation === 'column' ? 'price-promos-column' : ''">
        <!-- Полоса (сверху/снизу): фото + текст в ряд -->
        <template v-if="orientation !== 'column'">
            <div
                v-for="(promo, index) in promos"
                :key="promo.id || index"
                class="row align-items-center g-4 price-promo-band"
                :class="{ 'flex-row-reverse': promo.image && !promo.image_on_left }"
            >
                <div v-if="promo.image" :class="imageColClass(promo)">
                    <button type="button" class="promo-image-btn" @click="openLightbox(promo)" title="Открыть фото полностью">
                        <img :src="promo.image" :alt="promo.title || 'Акция'" class="promo-image" loading="lazy" />
                        <span class="promo-image-zoom"><i class="fas fa-expand"></i></span>
                    </button>
                </div>
                <div :class="promo.image ? textColClass(promo) : 'col-12'">
                    <h2 v-if="promo.title" class="fw-bold mb-3 promo-title">{{ promo.title }}</h2>
                    <p
                        v-for="(para, pi) in splitParagraphs(promo.description)"
                        :key="pi"
                        class="mb-2 text-muted promo-text"
                    >{{ para }}</p>
                </div>
            </div>
        </template>

        <!-- Колонка (слева/справа от категорий): фото над текстом -->
        <template v-else>
            <div
                v-for="(promo, index) in promos"
                :key="promo.id || index"
                class="price-promo-col mb-4"
            >
                <button v-if="promo.image" type="button" class="promo-image-btn" @click="openLightbox(promo)" title="Открыть фото полностью">
                    <img :src="promo.image" :alt="promo.title || 'Акция'" class="promo-image" loading="lazy" />
                    <span class="promo-image-zoom"><i class="fas fa-expand"></i></span>
                </button>
                <h3 v-if="promo.title" class="fw-bold mt-3 mb-2 promo-title-col">{{ promo.title }}</h3>
                <p
                    v-for="(para, pi) in splitParagraphs(promo.description)"
                    :key="pi"
                    class="mb-2 text-muted promo-text small"
                >{{ para }}</p>
            </div>
        </template>

        <!-- Полноэкранный просмотр -->
        <transition name="fade">
            <div
                v-if="lightbox"
                class="promo-lightbox-overlay"
                @click.self="closeLightbox"
                @touchstart="onTouchStart"
                @touchmove="onTouchMove"
                @touchend="onTouchEnd"
            >
                <button class="promo-lightbox-close" @click="closeLightbox" aria-label="Закрыть">&times;</button>
                <img
                    :src="lightbox.image"
                    :alt="lightbox.title || 'Акция'"
                    class="promo-lightbox-image"
                    :style="imageDragStyle"
                />
            </div>
        </transition>
    </div>
</template>

<script>
const sizeMap = {
    '3/4': { img: 'col-md-9', text: 'col-md-3' },
    '2/3': { img: 'col-md-8', text: 'col-md-4' },
    '1/2': { img: 'col-md-6', text: 'col-md-6' },
    '1/3': { img: 'col-md-4', text: 'col-md-8' },
    '1/4': { img: 'col-md-3', text: 'col-md-9' },
};

export default {
    name: 'PricePromos',
    props: {
        promos: {
            type: Array,
            default: () => [],
        },
        orientation: {
            type: String,
            default: 'band', // 'band' | 'column'
        },
    },
    data() {
        return {
            lightbox: null,
            touchStartY: null,
            dragY: 0,
        };
    },
    computed: {
        imageDragStyle() {
            if (!this.dragY) return {};
            const opacity = Math.max(0.3, 1 - Math.abs(this.dragY) / 400);
            return {
                transform: `translateY(${this.dragY}px)`,
                opacity,
                transition: 'none',
            };
        },
    },
    methods: {
        imageColClass(promo) {
            return (sizeMap[promo.image_size] || sizeMap['1/2']).img;
        },
        textColClass(promo) {
            return (sizeMap[promo.image_size] || sizeMap['1/2']).text;
        },
        splitParagraphs(text) {
            if (!text) return [];
            return text.split(/\n+/).map((s) => s.trim()).filter(Boolean);
        },
        openLightbox(promo) {
            this.lightbox = promo;
            this.dragY = 0;
            document.body.style.overflow = 'hidden';
        },
        closeLightbox() {
            this.lightbox = null;
            this.dragY = 0;
            this.touchStartY = null;
            document.body.style.overflow = '';
        },
        onTouchStart(e) {
            if (e.touches && e.touches.length === 1) {
                this.touchStartY = e.touches[0].clientY;
            }
        },
        onTouchMove(e) {
            if (this.touchStartY === null) return;
            const dy = e.touches[0].clientY - this.touchStartY;
            // тянем только вниз
            this.dragY = dy > 0 ? dy : 0;
        },
        onTouchEnd() {
            if (this.dragY > 110) {
                this.closeLightbox();
            } else {
                this.dragY = 0;
                this.touchStartY = null;
            }
        },
        onKeydown(e) {
            if (e.key === 'Escape' && this.lightbox) {
                this.closeLightbox();
            }
        },
    },
    mounted() {
        window.addEventListener('keydown', this.onKeydown);
    },
    beforeUnmount() {
        window.removeEventListener('keydown', this.onKeydown);
        document.body.style.overflow = '';
    },
};
</script>

<style scoped>
.price-promo-band {
    margin-bottom: 2.5rem;
}

.promo-image-btn {
    display: block;
    width: 100%;
    padding: 0;
    border: 0;
    background: none;
    cursor: zoom-in;
    position: relative;
    border-radius: 0.75rem;
    overflow: hidden;
}

/* Сохраняем исходные пропорции фото — без обрезки */
.promo-image {
    width: 100%;
    height: auto;
    display: block;
    border-radius: 0.75rem;
    transition: transform 0.3s ease;
}

.promo-image-btn:hover .promo-image {
    transform: scale(1.02);
}

.promo-image-zoom {
    position: absolute;
    top: 0.75rem;
    right: 0.75rem;
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: rgba(0, 0, 0, 0.55);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.2s;
}

.promo-image-btn:hover .promo-image-zoom {
    opacity: 1;
}

.promo-title {
    font-size: 1.6rem;
}

.promo-title-col {
    font-size: 1.15rem;
}

.promo-text {
    white-space: pre-line;
    line-height: 1.6;
}

/* Полноэкранный просмотр — фото на весь экран, без отступов и подписи */
.promo-lightbox-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.94);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 2000;
    touch-action: none;
}

.promo-lightbox-image {
    max-width: 100vw;
    max-height: 100vh;
    object-fit: contain;
    display: block;
    will-change: transform;
}

.promo-lightbox-close {
    position: absolute;
    top: max(12px, env(safe-area-inset-top));
    right: 18px;
    font-size: 2.6rem;
    line-height: 1;
    color: #fff;
    background: rgba(0, 0, 0, 0.35);
    border: none;
    border-radius: 50%;
    width: 48px;
    height: 48px;
    cursor: pointer;
    z-index: 2001;
}

.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.25s;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
