<template>
    <section v-if="show" id="video-cards" class="video-cards py-5" aria-labelledby="video-cards-title">
        <div class="container">
            <h2 id="video-cards-title" class="text-center fw-bold mb-2">{{ title }}</h2>
            <p v-if="subtitle" class="text-center text-muted mb-4">{{ subtitle }}</p>

            <div class="row g-4 justify-content-center">
                <div v-for="slide in slides" :key="slide.id" class="col-12 col-md-6 col-lg-4">
                    <article class="vc-card card h-100 border-0 shadow-sm">
                        <button
                            type="button"
                            class="vc-media"
                            :aria-label="`Смотреть видео: ${slide.title}`"
                            @click="lightbox = slide"
                        >
                            <img class="vc-bk" :src="slide.poster_url" alt="" loading="lazy" />
                            <video
                                :ref="(el) => setVideo(slide.id, el)"
                                class="vc-video"
                                :src="slide.preview_url || slide.video_url"
                                :poster="slide.poster_url"
                                muted
                                loop
                                playsinline
                                preload="none"
                                aria-hidden="true"
                            ></video>
                            <span class="vc-play"><i class="fas fa-play" aria-hidden="true"></i>Смотреть</span>
                        </button>

                        <div class="card-body d-flex flex-column">
                            <span class="badge rounded-pill text-bg-primary align-self-start mb-2">{{ slide.label }}</span>
                            <h3 class="h5 mb-2">{{ slide.title }}</h3>
                            <p v-if="slide.subtitle" class="text-muted mb-3">{{ slide.subtitle }}</p>
                            <div class="d-flex flex-wrap gap-2 mt-auto">
                                <HeroCtaButton
                                    v-if="slide.button"
                                    variant="primary"
                                    :label="slide.button.label"
                                    :icon="slide.button.icon"
                                    :href="slide.button.href"
                                />
                                <button type="button" class="btn btn-lg rounded-pill px-4 btn-outline-secondary" @click="lightbox = slide">
                                    <i class="fas fa-play me-2" aria-hidden="true"></i>Смотреть ролик
                                </button>
                            </div>
                        </div>
                    </article>
                </div>
            </div>
        </div>

        <VideoLightbox :slide="lightbox" @close="lightbox = null" />
    </section>
</template>

<script>
import HeroCtaButton from './HeroCtaButton.vue';
import VideoLightbox from './VideoLightbox.vue';
import { canAutoplayPreviews, setPlaying } from '@/utils/videoPlayback';

export default {
    name: 'VideoCards',
    components: { HeroCtaButton, VideoLightbox },
    props: {
        /** Ответ /api/guest/hero (data) */
        settings: { type: Object, default: null },
    },
    data() {
        return { lightbox: null, observer: null, videos: {}, autoplayAllowed: canAutoplayPreviews() };
    },
    computed: {
        slider() {
            return this.settings && this.settings.slider ? this.settings.slider : null;
        },
        slides() {
            return this.slider && Array.isArray(this.slider.slides) ? this.slider.slides : [];
        },
        show() {
            return !!(this.slider && this.slider.show_cards && this.slides.length > 0);
        },
        title() {
            return (this.slider && this.slider.cards_title) || 'Обучение по категориям';
        },
        subtitle() {
            return (this.slider && this.slider.cards_subtitle) || '';
        },
    },
    watch: {
        show(value) {
            if (value) {
                this.$nextTick(() => this.observeAll());
            }
        },
    },
    mounted() {
        this.observeAll();
    },
    beforeUnmount() {
        if (this.observer) {
            this.observer.disconnect();
        }
    },
    methods: {
        setVideo(id, el) {
            if (el) {
                this.videos[id] = el;
                if (this.observer) {
                    this.observer.observe(el);
                }
            } else {
                delete this.videos[id];
            }
        },
        observeAll() {
            if (!this.autoplayAllowed || typeof IntersectionObserver === 'undefined' || this.observer) {
                return;
            }
            // Играем превью только пока карточка на экране
            this.observer = new IntersectionObserver(
                (entries) => {
                    entries.forEach((entry) => setPlaying(entry.target, entry.isIntersecting));
                },
                { threshold: 0.35 }
            );
            Object.values(this.videos).forEach((el) => this.observer.observe(el));
        },
    },
};
</script>

<style scoped>
.video-cards {
    background: #f6f7f9;
}

.vc-media {
    position: relative;
    display: block;
    width: 100%;
    aspect-ratio: 4 / 5;
    padding: 0;
    border: 0;
    overflow: hidden;
    background: #111;
    cursor: pointer;
}

.vc-media:focus-visible {
    outline: 3px solid #0d6efd;
    outline-offset: -3px;
}

.vc-bk {
    position: absolute;
    inset: -12%;
    width: 124%;
    height: 124%;
    object-fit: cover;
    filter: blur(22px) brightness(0.65);
}

.vc-video {
    position: relative;
    display: block;
    width: 100%;
    height: 100%;
    object-fit: contain;
    pointer-events: none;
}

.vc-play {
    position: absolute;
    left: 12px;
    bottom: 12px;
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
</style>
