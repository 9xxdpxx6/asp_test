<template>
    <div class="container-fluid mt-5">
        <h2 class="text-center display-4 mb-4">Новости</h2>
        <div v-if="loading" class="text-center w-100">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Загрузка...</span>
            </div>
        </div>
        <div v-else class="row">
            <div class="col-md-3" v-for="post in posts" :key="post.id">
                <div class="card h-100">
                    <img v-if="post.preview" :src="post.preview" class="card-img-top post-image" alt="post image">
                    <img v-else :src="noImage" class="card-img-top post-image" alt="post image">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title display-6">{{ post.title }}</h5>
                        <router-link :to="{ name: 'post', params: { id: post.id } }" class="btn btn-outline-primary mt-auto">Читать больше</router-link>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-center my-4">
            <router-link :to="{ name: 'blog' }" class="btn btn-primary">Посмотреть все новости</router-link>
        </div>
    </div>
</template>

<script>
import axios from 'axios'
import API_ENDPOINTS from '@/services/api'

export default {
    name: 'Posts',

    data() {
        return {
            noImage: '/images/no-image.png',
            posts: [],
            loading: true,
        }
    },

    mounted() {
        // Загрузка новостей из API
        axios.get(API_ENDPOINTS.posts)
            .then(response => {
                this.posts = response.data.data.slice(0, 4)
            })
            .catch(error => {
                console.error('Ошибка при загрузке новостей:', error)
            })
            .finally(() => {
                this.loading = false
            })
    },

}
</script>

<style scoped>
.post-image {
    height: 250px; /* Задайте фиксированную высоту */
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
