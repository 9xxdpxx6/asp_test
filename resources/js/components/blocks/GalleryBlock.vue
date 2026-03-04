<template>
    <div class="gallery-block">
        <h3 v-if="content.title" class="text-center mb-4">{{ content.title }}</h3>
        <div class="row g-3">
            <div class="col-6 col-md-4 col-lg-3" v-for="(image, index) in content.images" :key="index">
                <div class="gallery-item" @click="openLightbox(index)">
                    <img :src="image.url" :alt="image.alt || ''" class="img-fluid rounded shadow-sm" />
                </div>
            </div>
        </div>

        <!-- Лайтбокс -->
        <transition name="fade">
            <div v-if="lightboxOpen" class="lightbox-overlay" @click.self="closeLightbox">
                <button class="lightbox-close" @click="closeLightbox">&times;</button>
                <button class="lightbox-prev" @click="prevImage" v-if="content.images.length > 1">&#8249;</button>
                <img :src="content.images[lightboxIndex].url"
                     :alt="content.images[lightboxIndex].alt || ''"
                     class="lightbox-image" />
                <button class="lightbox-next" @click="nextImage" v-if="content.images.length > 1">&#8250;</button>
            </div>
        </transition>
    </div>
</template>

<script>
export default {
    name: 'GalleryBlock',
    props: {
        content: {
            type: Object,
            required: true,
        },
    },
    data() {
        return {
            lightboxOpen: false,
            lightboxIndex: 0,
        }
    },
    methods: {
        openLightbox(index) {
            this.lightboxIndex = index
            this.lightboxOpen = true
        },
        closeLightbox() {
            this.lightboxOpen = false
        },
        prevImage() {
            this.lightboxIndex = (this.lightboxIndex - 1 + this.content.images.length) % this.content.images.length
        },
        nextImage() {
            this.lightboxIndex = (this.lightboxIndex + 1) % this.content.images.length
        },
    },
}
</script>

<style scoped>
.gallery-item {
    cursor: pointer;
    overflow: hidden;
    border-radius: 0.375rem;
    transition: transform 0.2s;
}
.gallery-item:hover {
    transform: scale(1.03);
}
.gallery-item img {
    width: 100%;
    height: 180px;
    object-fit: cover;
}

.lightbox-overlay {
    position: fixed;
    top: 0; left: 0; width: 100%; height: 100%;
    background: rgba(0,0,0,0.9);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 2000;
}
.lightbox-image {
    max-width: 90%;
    max-height: 85vh;
    border-radius: 0.5rem;
}
.lightbox-close {
    position: absolute; top: 20px; right: 30px;
    font-size: 2.5rem; color: #fff; background: none;
    border: none; cursor: pointer; z-index: 2001;
}
.lightbox-prev, .lightbox-next {
    position: absolute; top: 50%; transform: translateY(-50%);
    font-size: 3rem; color: #fff; background: none;
    border: none; cursor: pointer; z-index: 2001; padding: 1rem;
}
.lightbox-prev { left: 20px; }
.lightbox-next { right: 20px; }

.fade-enter-active, .fade-leave-active { transition: opacity 0.3s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
