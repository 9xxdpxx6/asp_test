<template>
    <div class="hv" :class="[`hv--${side}`, `hv-ta-${textAlign}`, `hv-va-${textValign}`]">
        <div class="hv-backdrop" :style="{ backgroundImage: `url(${slide.poster_url})` }"></div>
        <video
            v-if="side === 'background'"
            ref="bgvideo"
            class="hv-bgvideo"
            :src="previewSrc"
            :poster="slide.poster_url"
            muted
            loop
            playsinline
            preload="none"
            aria-hidden="true"
        ></video>
        <div class="hv-overlay"></div>

        <div class="container hv-inner">
            <div class="hv-text">
                <h2 class="hv-title">{{ slide.title }}</h2>
                <p v-if="slide.subtitle" class="hv-sub">{{ slide.subtitle }}</p>
                <div class="hv-actions">
                    <HeroCtaButton
                        v-if="slide.button"
                        variant="light"
                        :label="slide.button.label"
                        :icon="slide.button.icon"
                        :href="slide.button.href"
                    />
                    <button
                        v-if="side === 'background'"
                        type="button"
                        class="btn btn-lg rounded-pill px-4 btn-outline-light"
                        @click="$emit('open', slide)"
                    >
                        <i class="fas fa-volume-high me-2" aria-hidden="true"></i>Смотреть со звуком
                    </button>
                </div>
            </div>

            <button
                v-if="side !== 'background'"
                type="button"
                class="hv-media"
                :class="[mediaShape, `hv-size-${slide.size}`]"
                :style="{ '--hv-ar': slide.aspect_css }"
                :aria-label="`Смотреть видео со звуком: ${slide.title}`"
                @click="$emit('open', slide)"
            >
                <img v-if="slide.fit === 'contain'" class="hv-bk" :src="slide.poster_url" alt="" />
                <video
                    ref="video"
                    class="hv-video"
                    :style="{ objectFit: slide.fit }"
                    :src="previewSrc"
                    :poster="slide.poster_url"
                    muted
                    loop
                    playsinline
                    preload="none"
                    aria-hidden="true"
                ></video>
                <span class="hv-play"><i class="fas fa-play" aria-hidden="true"></i>Со звуком</span>
            </button>
        </div>
    </div>
</template>

<script>
import HeroCtaButton from './HeroCtaButton.vue';
import { canAutoplayPreviews, setPlaying } from '@/utils/videoPlayback';

export default {
    name: 'HeroVideoSlide',
    components: { HeroCtaButton },
    props: {
        slide: { type: Object, required: true },
        /** Слайд сейчас на экране: только тогда крутим превью */
        active: { type: Boolean, default: false },
    },
    emits: ['open'],
    data() {
        return { autoplayAllowed: canAutoplayPreviews() };
    },
    computed: {
        side() {
            return this.slide.side || 'right';
        },
        textAlign() {
            return ['left', 'center', 'right'].includes(this.slide.text_align) ? this.slide.text_align : 'left';
        },
        textValign() {
            return ['top', 'center', 'bottom'].includes(this.slide.text_valign) ? this.slide.text_valign : 'center';
        },
        previewSrc() {
            return this.slide.preview_url || this.slide.video_url;
        },
        mediaShape() {
            return this.slide.aspect > 1.2 ? 'hv-media--land' : 'hv-media--port';
        },
    },
    watch: {
        active() {
            this.sync();
        },
    },
    mounted() {
        this.sync();
    },
    beforeUnmount() {
        setPlaying(this.$refs.video || this.$refs.bgvideo, false);
    },
    methods: {
        sync() {
            setPlaying(this.$refs.video || this.$refs.bgvideo, this.active && this.autoplayAllowed);
        },
    },
};
</script>

<style scoped>
.hv {
    position: relative;
    height: 100%;
    min-height: 550px;
    overflow: hidden;
    color: #fff;
    background: #14161a;
    display: flex;
    align-items: stretch;
}

.hv-backdrop {
    position: absolute;
    inset: -30px;
    background-size: cover;
    background-position: center;
    filter: blur(38px) brightness(0.55) saturate(1.1);
}

.hv-bgvideo {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.hv-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(to bottom, rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.55));
}

.hv-inner {
    position: relative;
    z-index: 2;
    flex: 1 1 auto;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 3rem;
    padding-top: 2.5rem;
    padding-bottom: 5.5rem;
}

/* Запас под стрелки слайдера по бокам */
@media (min-width: 992px) {
    .hv-inner {
        padding-left: 3.5rem;
        padding-right: 3.5rem;
    }
}

.hv--left .hv-inner {
    flex-direction: row-reverse;
}

.hv--background .hv-inner {
    flex-direction: column;
    justify-content: center;
}

.hv--background .hv-text {
    flex: 0 0 auto;
    align-self: stretch;
}

.hv--background.hv-va-top .hv-inner { justify-content: flex-start; }
.hv--background.hv-va-bottom .hv-inner { justify-content: flex-end; }

.hv-text {
    flex: 1 1 0;
    min-width: 0;
    text-align: left;
}

/* Выравнивание текста по горизонтали */
.hv-ta-center .hv-text { text-align: center; }
.hv-ta-right .hv-text { text-align: right; }
.hv-ta-center .hv-sub { margin-left: auto; margin-right: auto; }
.hv-ta-right .hv-sub { margin-left: auto; }
.hv-ta-center .hv-actions { justify-content: center; }
.hv-ta-right .hv-actions { justify-content: flex-end; }

/* По высоте: только когда видео рядом с текстом на широком экране (на телефоне всё идёт столбиком) */
@media (min-width: 992px) {
    .hv-va-top:not(.hv--background) .hv-text { align-self: flex-start; }
    .hv-va-bottom:not(.hv--background) .hv-text { align-self: flex-end; }
}

.hv-title {
    font-size: 2.4rem;
    line-height: 1.15;
    font-weight: 700;
    margin: 0 0 0.75rem;
    text-shadow: 0 2px 8px rgba(0, 0, 0, 0.45);
}

.hv-sub {
    font-size: 1.15rem;
    line-height: 1.5;
    max-width: 34rem;
    margin: 0 0 1.5rem;
    text-shadow: 0 1px 4px rgba(0, 0, 0, 0.3);
}

.hv-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
}

/* рамка видео */
.hv-media {
    position: relative;
    flex: 0 0 auto;
    aspect-ratio: var(--hv-ar);
    padding: 0;
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-radius: 18px;
    overflow: hidden;
    background: #000;
    box-shadow: 0 10px 34px rgba(0, 0, 0, 0.55);
    cursor: pointer;
    max-width: 100%;
}

.hv-media:focus-visible {
    outline: 3px solid #fff;
    outline-offset: 3px;
}

.hv-media--port.hv-size-s { height: 340px; }
.hv-media--port.hv-size-m { height: 410px; }
.hv-media--port.hv-size-l { height: 480px; }
.hv-media--land.hv-size-s { width: 380px; }
.hv-media--land.hv-size-m { width: 470px; }
.hv-media--land.hv-size-l { width: 560px; }

.hv-bk {
    position: absolute;
    inset: -12%;
    width: 124%;
    height: 124%;
    object-fit: cover;
    filter: blur(22px) brightness(0.65);
}

.hv-video {
    position: relative;
    display: block;
    width: 100%;
    height: 100%;
    pointer-events: none;
}

.hv-play {
    position: absolute;
    left: 10px;
    bottom: 10px;
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    border-radius: 99px;
    background: rgba(0, 0, 0, 0.65);
    color: #fff;
    font-size: 0.8rem;
    font-weight: 600;
}

@media (max-width: 991.98px) {
    .hv-inner {
        flex-direction: column !important;
        gap: 1.25rem;
        align-items: stretch !important;
        padding-top: 1.75rem;
        padding-bottom: 5rem;
    }

    .hv-media { align-self: center; }
    .hv-title { font-size: 1.7rem; }
    .hv-sub { font-size: 1rem; }

    .hv-media--port.hv-size-s { height: 240px; }
    .hv-media--port.hv-size-m { height: 300px; }
    .hv-media--port.hv-size-l { height: 340px; }
    .hv-media--land { width: 100%; max-width: 420px; }
}
</style>
