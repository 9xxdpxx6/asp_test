<template>
    <section class="section-spacing">
        <div class="container">
            <div class="text-center mb-5">
                <h1 class="display-5 fw-bold section-title">Программы лояльности</h1>
                <p class="text-muted lead mt-3">Специальные предложения и скидки для наших учеников</p>
            </div>

            <div v-if="loading" class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Загрузка...</span>
                </div>
            </div>

            <div v-else-if="discounts.length > 0" class="row g-4">
                <div class="col-sm-6 col-lg-4" v-for="discount in discounts" :key="discount.id">
                    <div class="card h-100 shadow-sm card-hover">
                        <img
                            v-if="discount.preview"
                            :src="discount.preview"
                            class="card-img-top img-ratio-3x2"
                            :alt="discount.title"
                            loading="lazy"
                        >
                        <img
                            v-else
                            :src="noImage"
                            class="card-img-top img-ratio-3x2"
                            alt="discount image"
                            loading="lazy"
                        >
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title fw-bold">{{ discount.title }}</h5>
                            <p v-if="discount.excerpt" class="text-muted small flex-grow-1">{{ discount.excerpt }}</p>
                            <router-link
                                :to="{ name: 'discount', params: { id: discount.slug || String(discount.id) } }"
                                class="btn btn-outline-primary mt-auto rounded-pill"
                            >
                                Подробнее
                            </router-link>
                        </div>
                    </div>
                </div>
            </div>

            <div v-else class="text-center py-5">
                <i class="fas fa-tags fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">На данный момент программы лояльности отсутствуют</h4>
            </div>
        </div>
    </section>
</template>

<script>
import axios from "axios";
import API_ENDPOINTS from '@/services/api.js';

export default {
    name: "Discount",

    data() {
        return {
            noImage: '/images/no-image.png',
            discounts: [],
            loading: true,
        };
    },

    mounted() {
        axios.get(API_ENDPOINTS.discounts)
            .then(response => {
                this.discounts = response.data.data;
            })
            .catch(error => {
                console.error('Ошибка при загрузке программ лояльности:', error);
            })
            .finally(() => {
                this.loading = false;
            });
    },
};
</script>

<style scoped>
</style>
