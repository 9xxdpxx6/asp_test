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
                <p class="lead post-content text-wrap" v-html="safeContent"></p>
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

                this.$nextTick(() => {
                    const images = document.querySelectorAll('.post-content img')
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
        safeContent() {
            // Очищаем контент поста от потенциально вредного HTML
            return this.post ? DOMPurify.sanitize(this.post.content) : ''
        },
    },

}
</script>

<style>
.post-content img {
    max-width: 100%; /* Изображение не выходит за пределы контейнера */
    height: auto; /* Сохраняет пропорции изображения */
    width: auto; /* Устраняет обрезку при наличии фиксированной ширины */
    display: block; /* Убирает inline-отступы */
    margin: 0 auto; /* Центрирует изображение */
}
.post-content {
    max-width: 100%; /* Контент не выходит за пределы контейнера */
    overflow: hidden; /* Убирает горизонтальную прокрутку */
}
</style>
