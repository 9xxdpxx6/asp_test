<template>
    <div class="container my-5">
        <div v-if="loading" class="text-center w-100">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Загрузка...</span>
            </div>
        </div>
        <div v-else-if="category">
            <!-- Верхний блок с названием, превью, длительностью и ценой -->
            <div class="row align-items-center mb-4">
                <div class="col-md-8 lead">
                    <div class="d-flex flex-row">
                        <h2 class="display-4 mb-3 w-75">{{ category.name }}</h2>
                        <div class="icon-container ms-auto display-4">
                            <i :class="category.icon"></i>
                        </div>
                    </div>
                    <p class="mb-2"><strong class="fw-bold">Длительность:</strong> {{ category.duration }} часов</p>
                    <p><strong class="fw-bold">Цена:</strong> {{ Math.floor(category.price) }} руб.</p>
                </div>
            </div>

            <!-- Описание на всю ширину контейнера -->
            <div>
                <h4 class="mb-3">Описание:</h4>
                <p class="lead category-description text-wrap" v-html="safeDescription"></p>
            </div>

            <!-- Кнопка для возврата -->
            <div class="text-center mt-4">
                <router-link :to="{ name: 'prices' }" class="btn btn-primary">
                    Вернуться к категориям
                </router-link>
            </div>
        </div>
        <div v-else>
            <p class="text-center">Категория не найдена.</p>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import API_ENDPOINTS from '@/services/api';
import DOMPurify from "dompurify";

export default {
    name: 'CategoryDetail',

    data() {
        return {
            category: null,
            loading: true,
        }
    },

    mounted() {
        axios.get(API_ENDPOINTS.categoryDetails(this.$route.params.id))
            .then(response => {
                this.category = response.data.data
                console.log(this.category)
            })
            .catch(error => {
                console.error('Ошибка при загрузке категории:', error)
            })
            .finally(() => {
                this.loading = false

                this.$nextTick(() => {
                    const images = document.querySelectorAll('.category-description img')
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
            return this.category ? DOMPurify.sanitize(this.category.description) : ''
        },
    },
};
</script>

<style>
.category-description img {
    max-width: 100%; /* Изображение не выходит за пределы контейнера */
    height: auto; /* Сохраняет пропорции изображения */
    width: auto; /* Устраняет обрезку при наличии фиксированной ширины */
    display: block; /* Убирает inline-отступы */
    margin: 0 auto; /* Центрирует изображение */
}
.category-description {
    max-width: 100%; /* Контент не выходит за пределы контейнера */
    overflow: hidden; /* Убирает горизонтальную прокрутку */
}
</style>
