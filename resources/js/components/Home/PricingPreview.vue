<template>
    <section id="pricing-preview" class="section-spacing bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-6 section-title">Стоимость обучения</h2>
                <p class="text-muted mt-3">Выберите подходящую категорию и запишитесь на обучение</p>
            </div>

            <div v-if="loading" class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Загрузка...</span>
                </div>
            </div>

            <div v-else class="row g-4 justify-content-center">
                <div class="col-sm-6 col-lg-4" v-for="category in categories" :key="category.id">
                    <div class="card h-100 shadow-sm card-hover text-center">
                        <div v-if="category.image" class="card-img-top-wrapper">
                            <img :src="category.image" :alt="category.name" class="img-ratio-3x2 rounded-top" loading="lazy">
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title fw-bold" v-html="formatCategoryName(category.name)"></h5>
                            <p class="display-6 fw-bold text-primary my-2">
                                {{ formatPrice(category.price) }} ₽
                            </p>
                            <div class="mt-auto">
                                <button class="btn btn-primary rounded-pill w-100 mb-2" @click="openEnrollModal(category)">
                                    <i class="fas fa-pen me-1"></i>Записаться
                                </button>
                                <router-link :to="{ name: 'category', params: { id: category.id } }" class="btn btn-outline-primary rounded-pill w-100">
                                    Подробнее
                                </router-link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center mt-4">
                <router-link :to="{ name: 'prices' }" class="btn btn-outline-primary rounded-pill px-4">
                    Все категории и цены <i class="fas fa-arrow-right ms-1"></i>
                </router-link>
            </div>
        </div>

        <!-- Enroll modal -->
        <CallbackForm
            v-if="isModalOpen"
            :category="selectedCategory"
            @close="closeModal"
        />
    </section>
</template>

<script>
import axios from 'axios';
import API_ENDPOINTS from '@/services/api';
import { formatCategoryName } from '@/utils/formatCategoryName';
import CallbackForm from '@/components/CallbackForm.vue';

export default {
    name: 'PricingPreview',
    components: { CallbackForm },
    data() {
        return {
            categories: [],
            loading: true,
            isModalOpen: false,
            selectedCategory: null,
        };
    },
    methods: {
        formatCategoryName,
        formatPrice(price) {
            return Math.floor(price).toLocaleString('ru-RU');
        },
        openEnrollModal(category) {
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
                this.categories = response.data.data.slice(0, 4);
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

:deep(.category-marker) {
    font-weight: 800;
}
</style>
