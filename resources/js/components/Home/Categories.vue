<template>
    <div class="container mt-5">
        <h2 class="text-center mb-4 display-4">Категории обучения</h2>
        <div v-if="loading" class="text-center w-100">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Загрузка...</span>
            </div>
        </div>
        <div v-else class="row text-center">
            <div class="col-md-4 mb-4" v-for="category in categories" :key="category.id">
                <div class="card h-100 shadow-sm position-relative">
                    <div v-if="category.image" class="card-img-top-wrapper">
                        <img :src="category.image" :alt="category.name" class="card-img-top category-card-img">
                    </div>
                    <div v-else class="icon-container position-absolute top-0 end-0 p-3 display-4">
                        <i :class="category.icon"></i>
                    </div>
                    <div class="card-body">
                        <h2 class="card-title" v-html="formatCategoryName(category.name)"></h2>
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
            <div>
                <router-link class="btn btn-primary" :to="{ name: 'prices' }">Посмотреть все категории</router-link>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios'
import API_ENDPOINTS from '@/services/api'
import { formatCategoryName } from '@/utils/formatCategoryName'

export default {

    name: 'Categories',

    data() {
        return {
            categories: [],
            loading: true,
        }
    },

    methods: {
        formatCategoryName,
    },

    mounted() {
        // Выполняем API-запрос к серверу
        axios.get(API_ENDPOINTS.categories)

            .then(response => {
                //console.log(response.data.data)

                this.categories = response.data.data.slice(0, 3)
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
.card-img-top-wrapper {
    width: 100%;
    aspect-ratio: 1;
    overflow: hidden;
}
.category-card-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.card {
    transition: transform 0.2s;
}
.card:hover {
    transform: scale(1.05);
}

:deep(.category-marker) {
    font-weight: 800;
}
</style>
