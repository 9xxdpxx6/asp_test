<template>
    <div class="container my-5">
        <div v-if="loading" class="text-center w-100">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Загрузка...</span>
            </div>
        </div>
        <div v-else-if="discount">
            <!-- Верхний блок с названием, превью, длительностью и ценой -->
            <div class="row align-items-center mb-4">
                <div class="col-md-8 lead">
                    <div class="d-flex flex-row">
                        <h2 class="display-4 mb-3 w-75">{{ discount.name }}</h2>
                    </div>
                    <p class="mb-2"><strong class="fw-bold">Размер скидки:</strong> {{ discount.percentage }} %</p>
                </div>
            </div>

            <div>
                <h4 class="mb-3">Описание:</h4>
                <p class="lead discount-description text-wrap" v-html="safeDescription"></p>
            </div>

            <div class="text-center mt-4">
                <router-link :to="{ name: 'discounts' }" class="btn btn-primary">
                    Вернуться к категориям
                </router-link>
            </div>
        </div>
        <div v-else>
            <p class="text-center">Элемент не найдет.</p>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import API_ENDPOINTS from '@/services/api';
import DOMPurify from "dompurify";

export default {
    name: 'DiscountDetail',

    data() {
        return {
            discount: null,
            loading: true,
        }
    },

    mounted() {
        axios.get(API_ENDPOINTS.discountDetails(this.$route.params.id))
            .then(response => {
                this.discount = response.data.data
                console.log(this.discount)
            })
            .catch(error => {
                console.error('Ошибка при загрузке категории:', error)
            })
            .finally(() => {
                this.loading = false

                this.$nextTick(() => {
                    const images = document.querySelectorAll('.discount-description img')
                    images.forEach(img => {
                        img.onload = () => {
                            const containerWidth = img.parentElement.offsetWidth
                            if (img.naturalWidth > containerWidth) {
                                img.style.maxWidth = '100%' // Ограничиваем ширину
                                img.style.height = 'auto' // Сохраняем пропорции
                            }
                        }
                    })
                })
            })
    },

    computed: {
        safeDescription() {
            // Очищаем описание от потенциально вредного HTML
            return this.discount ? DOMPurify.sanitize(this.discount.description) : ''
        },
    },
};
</script>

<style>

</style>
