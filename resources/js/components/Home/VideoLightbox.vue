<template>
    <Teleport to="body">
        <transition name="vl-fade">
            <div
                v-if="slide"
                ref="root"
                class="vl"
                role="dialog"
                aria-modal="true"
                :aria-label="slide.title"
                tabindex="-1"
                @click.self="close"
                @keydown="onKeydown"
            >
                <button ref="closeBtn" type="button" class="vl-close" aria-label="Закрыть видео" @click="close">
                    <i class="fas fa-xmark" aria-hidden="true"></i>
                </button>

                <div class="vl-box">
                    <video
                        :key="slide.id"
                        ref="video"
                        class="vl-video"
                        :src="slide.video_url"
                        :poster="slide.poster_url"
                        :style="{ aspectRatio: slide.aspect_css }"
                        controls
                        autoplay
                        playsinline
                        preload="auto"
                    ></video>

                    <div class="vl-caption">
                        <strong class="vl-title">{{ slide.title }}</strong>
                        <p v-if="slide.subtitle" class="vl-sub">{{ slide.subtitle }}</p>
                        <div v-if="slide.button" class="vl-cta" @click="close">
                            <HeroCtaButton
                                variant="light"
                                :label="slide.button.label"
                                :icon="slide.button.icon"
                                :href="slide.button.href"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </transition>
    </Teleport>
</template>

<script>
import HeroCtaButton from './HeroCtaButton.vue';

export default {
    name: 'VideoLightbox',
    components: { HeroCtaButton },
    props: {
        /** Слайд из /api/guest/hero (slider.slides[]) или null, если окно закрыто */
        slide: { type: Object, default: null },
    },
    emits: ['close'],
    data() {
        return { opener: null };
    },
    watch: {
        slide: {
            immediate: false,
            handler(value) {
                if (value) {
                    this.onOpen();
                } else {
                    this.onClose();
                }
            },
        },
    },
    beforeUnmount() {
        this.unlockScroll();
    },
    methods: {
        close() {
            this.$emit('close');
        },
        onOpen() {
            this.opener = document.activeElement;
            this.lockScroll();
            this.$nextTick(() => {
                if (this.$refs.closeBtn) {
                    this.$refs.closeBtn.focus();
                }
            });
        },
        onClose() {
            this.unlockScroll();
            const opener = this.opener;
            this.opener = null;
            if (opener && typeof opener.focus === 'function' && document.contains(opener)) {
                opener.focus();
            }
        },
        lockScroll() {
            const scrollbar = window.innerWidth - document.documentElement.clientWidth;
            document.body.style.overflow = 'hidden';
            if (scrollbar > 0) {
                document.body.style.paddingRight = `${scrollbar}px`;
            }
        },
        unlockScroll() {
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
        },
        onKeydown(event) {
            if (event.key === 'Escape') {
                event.stopPropagation();
                this.close();
                return;
            }

            if (event.key !== 'Tab' || !this.$refs.root) {
                return;
            }

            // удерживаем Tab внутри окна
            const focusable = Array.from(
                this.$refs.root.querySelectorAll('button, a[href], video[controls], [tabindex]:not([tabindex="-1"])')
            ).filter((el) => !el.disabled && el.offsetParent !== null);
            if (focusable.length === 0) {
                return;
            }
            const first = focusable[0];
            const last = focusable[focusable.length - 1];
            if (event.shiftKey && document.activeElement === first) {
                event.preventDefault();
                last.focus();
            } else if (!event.shiftKey && document.activeElement === last) {
                event.preventDefault();
                first.focus();
            }
        },
    },
};
</script>

<style scoped>
.vl {
    position: fixed;
    inset: 0;
    z-index: 2000;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 16px;
    background: rgba(0, 0, 0, 0.88);
    overflow-y: auto;
    outline: none;
}

.vl-close {
    position: absolute;
    top: 14px;
    right: 14px;
    z-index: 2;
    width: 46px;
    height: 46px;
    border-radius: 50%;
    border: 0;
    background: #fff;
    color: #111;
    font-size: 20px;
    line-height: 1;
    cursor: pointer;
}

.vl-box {
    max-width: 100%;
    text-align: center;
    color: #fff;
}

.vl-video {
    display: block;
    margin: 0 auto;
    max-width: min(92vw, 960px);
    max-height: 74vh;
    border-radius: 12px;
    background: #000;
}

.vl-caption {
    margin-top: 14px;
    max-width: 560px;
    margin-left: auto;
    margin-right: auto;
}

.vl-title {
    display: block;
    font-size: 1.15rem;
}

.vl-sub {
    margin: 6px 0 0;
    opacity: 0.85;
}

.vl-cta {
    margin-top: 12px;
}

.vl-fade-enter-active,
.vl-fade-leave-active {
    transition: opacity 0.2s ease;
}

.vl-fade-enter-from,
.vl-fade-leave-to {
    opacity: 0;
}
</style>
