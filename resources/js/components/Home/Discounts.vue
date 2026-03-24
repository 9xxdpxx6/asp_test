<template>
    <section id="discounts-home" class="section-spacing bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-6 section-title">Скидки и акции</h2>
                <p class="text-muted mt-3 mb-0">Специальные предложения и программы лояльности</p>
            </div>

            <div v-if="loading" class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Загрузка...</span>
                </div>
            </div>

            <div v-else class="row g-4 justify-content-center">
                <div class="col-sm-6 col-lg-4" v-for="(discount, index) in discounts" :key="discount.id || index">
                    <div class="card h-100 shadow-sm card-hover text-center">
                        <div class="card-img-top-wrapper">
                            <img
                                :src="discount.image || '/images/no-image.png'"
                                :alt="discount.name"
                                class="img-ratio-3x2 rounded-top"
                                loading="lazy"
                            >
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title fw-bold">{{ discount.name }}</h5>
                            <p v-if="discount.description" class="text-muted small mb-2">{{ discount.description }}</p>
                            <p v-if="hasPercentage(discount)" class="display-6 fw-bold text-primary my-2">
                                {{ formatPercentage(discount.percentage) }}%
                            </p>
                            <div class="mt-auto pt-2">
                                <router-link
                                    v-if="detailParam(discount)"
                                    :to="{ name: 'discount', params: { id: detailParam(discount) } }"
                                    class="btn btn-outline-primary rounded-pill w-100"
                                >
                                    Подробнее
                                </router-link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center mt-4">
                <router-link :to="{ name: 'discounts' }" class="btn btn-outline-primary rounded-pill px-4">
                    Узнать о всех скидках <i class="fas fa-arrow-right ms-1"></i>
                </router-link>
            </div>
        </div>
    </section>
</template>

<script>
import axios from 'axios';
import API_ENDPOINTS from '@/services/api';

const FALLBACK_HOME_DISCOUNTS = [
    {
        id: 1,
        slug: null,
        name: 'Скидки учащимся и студентам РФ',
        description: 'Воспользуйтесь скидкой при получении образования.',
        image: '/images/discounts/discount-left.JPG',
        percentage: null,
    },
    {
        id: 2,
        slug: null,
        name: 'Оплата материнским капиталом',
        description: 'Используйте материнский капитал для обучения в автошколе.',
        image: '/images/discounts/discount-right.jpg',
        percentage: null,
    },
];

export default {
    name: 'Discounts',

    data() {
        return {
            discounts: [],
            loading: true,
        };
    },

    mounted() {
        axios.get(API_ENDPOINTS.discountsHome)
            .then((response) => {
                const data = Array.isArray(response.data?.data) ? response.data.data : [];
                this.discounts = data.length ? data : FALLBACK_HOME_DISCOUNTS;
            })
            .catch(() => {
                this.discounts = FALLBACK_HOME_DISCOUNTS;
            })
            .finally(() => {
                this.loading = false;
            });
    },

    methods: {
        detailParam(discount) {
            if (!discount) {
                return null;
            }
            if (discount.slug) {
                return discount.slug;
            }
            return discount.id != null ? String(discount.id) : null;
        },

        hasPercentage(discount) {
            const p = discount?.percentage;
            return p !== null && p !== undefined && p !== '';
        },

        formatPercentage(value) {
            const n = Number(value);
            if (Number.isNaN(n)) {
                return '';
            }
            if (Math.abs(n - Math.round(n)) < 0.001) {
                return String(Math.round(n));
            }
            return n.toFixed(2).replace(/\.?0+$/, '');
        },
    },
};
</script>

<style scoped>
.card-img-top-wrapper {
    overflow: hidden;
}
</style>
