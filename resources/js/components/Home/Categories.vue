<template>
    <div class="container mt-5">
        <h2 class="text-center mb-4 display-4">Категории обучения</h2>
        <div v-if="loading" class="text-center w-100">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Загрузка...</span>
            </div>
        </div>
        <div v-else class="row">
            <div class="col-md-4 mb-4" v-for="category in categories" :key="category.id">
                <div class="card h-100 shadow-sm position-relative">
                    <div class="icon-container position-absolute top-0 end-0 p-3 display-4">
                        <i :class="category.icon"></i>
                    </div>
                    <div class="card-body">
                        <h2 class="card-title w-75">{{ category.name }}</h2>
                    </div>
                    <div class="card-footer bg-transparent border-0">
                        <p class="h3">{{ category.price }} руб.</p>
                        <div class="row">
                            <div class="col-12">
                                <router-link :to="{ name: 'category', params: { id: category.id } }" class="btn btn-outline-primary w-100">
                                    Подробнее
                                </router-link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios'
import API_ENDPOINTS from '@/services/api'

export default {

    name: 'Categories',

    data() {
        return {
            categories: [],
            loading: true,
        }
    },

    mounted() {
        // Выполняем API-запрос к серверу
        axios.get(API_ENDPOINTS.categories)
            .then(response => {
                this.categories = response.data.data
                console.log(this.categories)
            })
            .catch(error => {
                console.error('Ошибка при загрузке категорий:', error)
            })
            .finally(() => {
                this.loading = false
            })
    },

}
</script>

<style scoped>
.category-image {
    height: 300px; /* Задайте фиксированную высоту */
    object-fit: cover; /* Сохраняет пропорции изображения */
    width: 100%; /* Растягивает изображение по ширине карточки */
}
.card {
    transition: transform 0.2s; /* Плавный эффект при наведении */
}
.card:hover {
    transform: scale(1.05); /* Увеличение карты при наведении */
}
</style>
