<template>
    <section class="section-spacing">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-6 section-title">Новости</h2>
            </div>

            <div v-if="loading" class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Загрузка...</span>
                </div>
            </div>

            <div v-else class="row g-4">
                <div class="col-sm-6 col-lg-3" v-for="post in posts" :key="post.id">
                    <div class="card h-100 shadow-sm card-hover">
                        <img
                            v-if="post.preview"
                            :src="post.preview"
                            class="card-img-top img-ratio-3x2"
                            :alt="post.title"
                            loading="lazy"
                        >
                        <img
                            v-else
                            :src="noImage"
                            class="card-img-top img-ratio-3x2"
                            alt="post image"
                            loading="lazy"
                        >
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title fw-bold">{{ post.title }}</h5>
                            <router-link
                                :to="{ name: 'post', params: { slug: post.slug } }"
                                class="btn btn-outline-primary mt-auto rounded-pill"
                            >
                                Читать больше
                            </router-link>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center mt-4">
                <router-link :to="{ name: 'blog' }" class="btn btn-outline-primary rounded-pill px-4">
                    Все новости <i class="fas fa-arrow-right ms-1"></i>
                </router-link>
            </div>
        </div>
    </section>
</template>

<script>
import axios from 'axios';
import API_ENDPOINTS from '@/services/api';

export default {
    name: 'Posts',

    data() {
        return {
            noImage: '/images/no-image.png',
            posts: [],
            loading: true,
        };
    },

    mounted() {
        axios.get(API_ENDPOINTS.posts)
            .then(response => {
                this.posts = response.data.data.slice(0, 4);
            })
            .catch(error => {
                console.error('Ошибка при загрузке новостей:', error);
            })
            .finally(() => {
                this.loading = false;
            });
    },
};
</script>

<style scoped>
</style>
