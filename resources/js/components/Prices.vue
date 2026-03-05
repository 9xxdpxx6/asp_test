<template>
    <section class="section-spacing">
        <div class="container">
            <div class="text-center mb-5">
                <h1 class="display-5 fw-bold section-title">Цены на обучение</h1>
                <p class="text-muted lead mt-3">Выберите категорию и запишитесь на обучение</p>
            </div>

            <div v-if="loading" class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Загрузка...</span>
                </div>
            </div>

            <div v-else class="row g-4">
                <div
                    class="col-md-6 col-lg-4"
                    v-for="category in categories"
                    :key="category.id"
                >
                    <div class="card shadow-sm card-hover h-100 overflow-hidden">
                        <div v-if="category.image" class="card-img-top-wrapper">
                            <img
                                :src="category.image"
                                :alt="category.name"
                                class="card-img-top img-ratio-3x2"
                                loading="lazy"
                            >
                        </div>
                        <div class="card-body d-flex flex-column p-4">
                            <h3 class="mb-2 category-title" v-html="formatCategoryName(category.name)"></h3>
                            <p class="display-6 fw-bold text-primary mb-3 category-price">
                                {{ formatPrice(category.price) }} ₽
                            </p>
                            <p
                                v-if="category.description"
                                class="text-muted mb-4 flex-grow-1 description-excerpt"
                            >{{ stripHtml(category.description) }}</p>
                            <div class="d-flex gap-2 flex-wrap mt-auto">
                                <router-link
                                    :to="{ name: 'category', params: { id: category.id } }"
                                    class="btn btn-outline-primary flex-fill"
                                >
                                    Подробнее
                                </router-link>
                                <button class="btn btn-primary flex-fill" @click="openModal(category)">
                                    <i class="fas fa-pen me-2"></i>Записаться
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center mt-5 p-4 bg-light rounded-3">
                <p class="mb-2 text-muted">Не знаете что выбрать?</p>
                <a href="tel:+79615262359" class="btn btn-primary rounded-pill px-4">
                    <i class="fas fa-phone me-2"></i>Позвоните нам
                </a>
            </div>
        </div>

        <CallbackForm
            v-if="isModalOpen"
            :category="selectedCategory"
            @close="closeModal"
        />
    </section>
</template>

<script>
import axios from "axios";
import API_ENDPOINTS from '@/services/api.js';
import { formatCategoryName } from '@/utils/formatCategoryName';
import CallbackForm from '@/components/CallbackForm.vue';

export default {
    name: 'Prices',
    components: { CallbackForm },

    data() {
        return {
            categories: [],
            loading: true,
            selectedCategory: null,
            isModalOpen: false,
        };
    },

    methods: {
        formatCategoryName,
        formatPrice(price) {
            return Math.floor(price).toLocaleString('ru-RU');
        },
        stripHtml(html) {
            if (!html) return '';
            const tmp = document.createElement('div');
            tmp.innerHTML = html;
            const text = tmp.textContent || tmp.innerText || '';
            // Limit to ~150 chars for a clean excerpt
            return text.length > 150 ? text.substring(0, 150).trim() + '…' : text;
        },
        openModal(category) {
            this.selectedCategory = category;
            this.isModalOpen = true;
        },
        closeModal() {
            this.isModalOpen = false;
            this.selectedCategory = null;
        },
    },

    mounted() {
        axios.get(API_ENDPOINTS.categories)
            .then(response => {
                this.categories = response.data.data;
            })
            .catch(error => {
                console.error('Ошибка при загрузке категорий:', error);
            })
            .finally(() => {
                this.loading = false;
            });
    },
};
</script>

<style scoped>
.card-img-top-wrapper {
    overflow: hidden;
}

.card-img-top {
    transition: transform 0.3s ease;
}

.card:hover .card-img-top {
    transform: scale(1.03);
}

.description-excerpt {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
    line-height: 1.6;
}

:deep(.category-marker) {
    font-weight: 900;
    color: #111827;
}

.category-title {
    font-weight: 400;
}

.category-price {
    text-align: right;
}
</style>
