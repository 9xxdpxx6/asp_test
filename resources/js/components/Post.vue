<template>
    <div class="container my-5">
        <div v-if="loading" class="text-center w-100">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Загрузка...</span>
            </div>
        </div>
        <div v-else-if="post">
            <!-- Верхний блок с названием и изображением -->
            <div class="row align-items-center mb-4">
                <div class="col-md-8">
                    <h2 class="display-4 mb-3">{{ post.title }}</h2>
                    <p><strong>Дата публикации:</strong> {{ formatDate(post.date) }}</p>
                </div>
            </div>

            <!-- Описание на всю ширину контейнера -->
            <div>
                <p class="lead post-content" v-html="safeContent"></p>
            </div>

            <!-- Кнопка для возврата -->
            <div class="text-center mt-4">
                <router-link :to="{ name: 'blog' }" class="btn btn-primary">Вернуться к списку новостей</router-link>
            </div>
        </div>
        <div v-else>
            <p class="text-center">Пост не найден.</p>
        </div>
    </div>
</template>

<script>
import axios from 'axios'
import API_ENDPOINTS from '@/services/api'
import DOMPurify from "dompurify"

export default {

    name: 'PostDetail',

    data() {
        return {
            post: null,
            loading: true,
        }
    },

    methods: {
        formatDate(dateString) {
            const date = new Date(dateString)

            const day = String(date.getDate()).padStart(2, '0')
            const month = String(date.getMonth() + 1).padStart(2, '0')
            const year = date.getFullYear()
            const hours = String(date.getHours()).padStart(2, '0')
            const minutes = String(date.getMinutes()).padStart(2, '0')

            return `${day}.${month}.${year} ${hours}:${minutes}`
        }
    },

    mounted() {
        axios.get(API_ENDPOINTS.postDetails(this.$route.params.id))
            .then(response => {
                this.post = response.data.data
                console.log(this.post)
            })
            .catch(error => {
                console.error('Ошибка при загрузке поста:', error)
            })
            .finally(() => {
                this.loading = false
            })
    },

    computed: {
        safeContent() {
            // Очищаем контент поста от потенциально вредного HTML
            return this.post ? DOMPurify.sanitize(this.post.content) : ''
        },
    },

}
</script>

<style scoped>
.post-preview-image {
    max-width: 100%;
    height: auto;
    object-fit: cover;
}

</style>
