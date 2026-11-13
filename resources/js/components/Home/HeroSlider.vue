<template>
    <HeroSection v-if="!hasSlider" :settings="settings" />

    <section
        v-else
        ref="root"
        class="hero-slider"
        aria-roledescription="carousel"
        aria-label="Слайдер на главной странице"
        @mouseenter="hovered = true"
        @mousemove="onMouseMove"
        @mouseleave="onMouseLeave"
        @focusin="focused = true"
        @focusout="focused = false"
        @keydown.left="prev"
        @keydown.right="next"
    >
        <div
            class="hs-stack"
            @pointerdown="onPointerDown"
            @pointerup="onPointerUp"
            @pointercancel="swipeStart = null"
            @click.capture="onClickCapture"
        >
            <div
                v-for="(item, i) in items"
                :id="`hero-pane-${item.key}`"
                :key="item.key"
                class="hs-pane"
                :class="{ 'is-on': i === active }"
                :role="items.length > 1 ? 'tabpanel' : 'group'"
                aria-roledescription="slide"
                :aria-labelledby="items.length > 1 ? `hero-tab-${item.key}` : null"
                :aria-label="items.length > 1 ? null : item.label"
            >
                <HeroSection v-if="item.type === 'main'" :settings="settings" />
                <HeroVideoSlide
                    v-else
                    :slide="item.slide"
                    :fallback-bg="settings && settings.background_image_url ? settings.background_image_url : ''"
                    :active="i === active && inView"
                    @open="openLightbox"
                />
            </div>
        </div>

        <button v-if="items.length > 1" type="button" class="hs-arrow hs-prev" :class="{ 'is-shown': edge === 'left' }" aria-label="Предыдущий слайд" @click="prev">
            <i class="fas fa-chevron-left" aria-hidden="true"></i>
        </button>
        <button v-if="items.length > 1" type="button" class="hs-arrow hs-next" :class="{ 'is-shown': edge === 'right' }" aria-label="Следующий слайд" @click="next">
            <i class="fas fa-chevron-right" aria-hidden="true"></i>
        </button>

        <div v-if="items.length > 1" class="hs-tabs" role="tablist" aria-label="Слайды">
            <button
                v-for="(item, i) in items"
                :key="item.key"
                :id="`hero-tab-${item.key}`"
                type="button"
                role="tab"
                class="hs-tab"
                :class="{ 'is-on': i === active }"
                :aria-selected="i === active ? 'true' : 'false'"
                :aria-controls="`hero-pane-${item.key}`"
                @click="go(i)"
            >
                {{ item.label }}
            </button>
        </div>

        <VideoLightbox :slide="lightbox" @close="lightbox = null" />
    </section>
</template>

<script>
import HeroSection from './HeroSection.vue';
import HeroVideoSlide from './HeroVideoSlide.vue';
import VideoLightbox from './VideoLightbox.vue';

export default {
    name: 'HeroSlider',
    components: { HeroSection, HeroVideoSlide, VideoLightbox },
    props: {
        /** Ответ /api/guest/hero (data). Слайдер включается полем slider.in_hero и наличием видео-слайдов. */
        settings: { type: Object, default: null },
    },
    data() {
        return {
            active: 0,
            hovered: false,
            edge: null,
            focused: false,
            inView: true,
            lightbox: null,
            swipeStart: null,
            swipedAt: 0,
            timer: null,
            observer: null,
            tabHidden: false,
        };
    },
    computed: {
        slider() {
            return this.settings && this.settings.slider ? this.settings.slider : null;
        },
        videoSlides() {
            return this.slider && Array.isArray(this.slider.slides) ? this.slider.slides : [];
        },
        hasSlider() {
            return !!(this.slider && this.slider.in_hero && this.videoSlides.length > 0);
        },
        items() {
            const videos = this.videoSlides.map((slide) => ({
                key: `v${slide.id}`,
                type: 'video',
                label: slide.label,
                slide,
            }));
            const main = { key: 'main', type: 'main', label: this.slider ? this.slider.main_label : 'Об автошколе' };

            return this.slider && this.slider.main_position === 'first' ? [main, ...videos] : [...videos, main];
        },
        autoplaySeconds() {
            return this.slider ? Number(this.slider.autoplay_seconds) || 0 : 0;
        },
        paused() {
            return this.hovered || this.focused || this.lightbox !== null || this.tabHidden || !this.inView;
        },
    },
    watch: {
        items() {
            if (this.active >= this.items.length) {
                this.active = 0;
            }
        },
        paused() {
            this.restartTimer();
        },
        autoplaySeconds() {
            this.restartTimer();
        },
        active() {
            this.restartTimer();
        },
    },
    mounted() {
        document.addEventListener('visibilitychange', this.onVisibility);
        if (typeof IntersectionObserver !== 'undefined' && this.$refs.root) {
            this.observer = new IntersectionObserver(
                (entries) => {
                    this.inView = entries.some((e) => e.isIntersecting);
                },
                { threshold: 0.2 }
            );
            this.observer.observe(this.$refs.root);
        }
        this.restartTimer();
    },
    beforeUnmount() {
        document.removeEventListener('visibilitychange', this.onVisibility);
        if (this.observer) {
            this.observer.disconnect();
        }
        clearTimeout(this.timer);
    },
    methods: {
        go(index) {
            const n = this.items.length;
            this.active = ((index % n) + n) % n;
        },
        next() {
            this.go(this.active + 1);
        },
        prev() {
            this.go(this.active - 1);
        },
        openLightbox(slide) {
            if (Date.now() - this.swipedAt < 350) {
                return;
            }
            this.lightbox = slide;
        },
        /**
         * Стрелки видны, только когда мышь рядом с ними: в зоне у края слайдера (заметно больше самой стрелки).
         * Саму зону делаем не элементом, а расчётом по координатам, чтобы она не перекрывала кнопки и видео под ней.
         */
        onMouseMove(event) {
            const root = this.$refs.root;
            if (!root) {
                return;
            }
            const rect = root.getBoundingClientRect();
            const zoneW = Math.min(160, rect.width * 0.18);
            const zoneH = 320;
            const dy = Math.abs(event.clientY - (rect.top + rect.height / 2));
            let edge = null;
            if (dy <= zoneH / 2) {
                if (event.clientX - rect.left <= zoneW) {
                    edge = 'left';
                } else if (rect.right - event.clientX <= zoneW) {
                    edge = 'right';
                }
            }
            if (edge !== this.edge) {
                this.edge = edge;
            }
        },
        onMouseLeave() {
            this.hovered = false;
            this.edge = null;
        },
        onVisibility() {
            this.tabHidden = document.hidden;
        },
        restartTimer() {
            clearTimeout(this.timer);
            if (this.autoplaySeconds <= 0 || this.items.length < 2 || this.paused) {
                return;
            }
            this.timer = setTimeout(() => this.next(), this.autoplaySeconds * 1000);
        },
        onPointerDown(event) {
            if (event.pointerType === 'mouse' && event.button !== 0) {
                return;
            }
            this.swipeStart = { x: event.clientX, y: event.clientY };
        },
        onPointerUp(event) {
            if (!this.swipeStart) {
                return;
            }
            const dx = event.clientX - this.swipeStart.x;
            const dy = event.clientY - this.swipeStart.y;
            this.swipeStart = null;
            if (Math.abs(dx) > 50 && Math.abs(dx) > Math.abs(dy) * 1.5) {
                this.swipedAt = Date.now();
                dx < 0 ? this.next() : this.prev();
            }
        },
        /** После свайпа браузер шлёт click на кнопку под пальцем: гасим его. */
        onClickCapture(event) {
            if (Date.now() - this.swipedAt < 350) {
                event.stopPropagation();
                event.preventDefault();
            }
        },
    },
};
</script>

<style scoped>
.hero-slider {
    position: relative;
    margin-top: -10px;
}

/* Все слайды лежат в одной ячейке: высота шапки = высота самого высокого слайда, при переключении ничего не прыгает */
.hs-stack {
    display: grid;
    touch-action: pan-y;
}

.hs-pane {
    grid-area: 1 / 1;
    visibility: hidden;
    opacity: 0;
    transition: opacity 0.35s ease, visibility 0s linear 0.35s;
    min-width: 0;
}

.hs-pane.is-on {
    visibility: visible;
    opacity: 1;
    transition: opacity 0.35s ease;
}

.hs-pane > :deep(.hero-section),
.hs-pane > :deep(.hv) {
    height: 100%;
    margin-top: 0;
}

.hs-arrow {
    position: absolute;
    top: 50%;
    z-index: 5;
    width: 44px;
    height: 44px;
    margin-top: -22px;
    border: 0;
    border-radius: 50%;
    background: rgba(0, 0, 0, 0.55);
    color: #fff;
    cursor: pointer;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.2s ease, background-color 0.2s ease;
}

/* Видна, пока мышь в зоне у края (см. onMouseMove), и при управлении с клавиатуры */
.hs-arrow.is-shown,
.hs-arrow:focus-visible {
    opacity: 1;
    pointer-events: auto;
}

.hs-arrow:hover {
    background: rgba(0, 0, 0, 0.8);
}

.hs-prev { left: 14px; }
.hs-next { right: 14px; }

.hs-tabs {
    position: absolute;
    z-index: 5;
    left: 0;
    right: 0;
    bottom: 22px;
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 8px;
    padding: 0 12px;
}

.hs-tab {
    padding: 8px 18px;
    border: 1px solid rgba(255, 255, 255, 0.55);
    border-radius: 99px;
    background: rgba(0, 0, 0, 0.4);
    color: #fff;
    font-weight: 600;
    font-size: 0.9rem;
    cursor: pointer;
}

.hs-tab.is-on {
    background: #fff;
    border-color: #fff;
    color: #111;
}

.hs-tab:focus-visible,
.hs-arrow:focus-visible {
    outline: 3px solid #fff;
    outline-offset: 2px;
}

/* Без мыши (телефон, планшет) стрелок нет вообще: листают свайпом и вкладками */
@media (hover: none), (max-width: 991.98px) {
    .hs-arrow { display: none; }
}

@media (max-width: 991.98px) {

    /* На телефоне вкладки идут одной строкой с горизонтальной прокруткой: любое число слайдов не налезает на видео */
    .hs-tabs {
        flex-wrap: nowrap;
        justify-content: flex-start;
        overflow-x: auto;
        scrollbar-width: none;
        -webkit-overflow-scrolling: touch;
    }

    .hs-tabs::-webkit-scrollbar { display: none; }
    .hs-tab { flex: 0 0 auto; white-space: nowrap; padding: 7px 14px; font-size: 0.82rem; }
    .hs-tab:first-child { margin-left: auto; }
    .hs-tab:last-child { margin-right: auto; }
}
</style>
