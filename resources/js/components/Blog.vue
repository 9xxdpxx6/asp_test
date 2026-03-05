<template>
    <section class="section-spacing">
        <div class="container">
            <div class="text-center mb-5">
                <h1 class="display-5 fw-bold section-title">Новости</h1>
                <p class="text-muted lead mt-3">Полезные статьи и последние события из жизни автошколы</p>
            </div>

            <!-- Фильтры и сортировка -->
            <div class="row mb-4">
                <div class="col-12 col-md-6 mb-3">
                    <input
                        v-model="filters.keyword"
                        @input="applyFilters"
                        type="text"
                        class="form-control"
                        placeholder="Поиск..."
                    />
                </div>
                <div class="col-12 col-md-6 mb-3 d-flex">
                    <select v-model="filters.sortOrder" @change="applyFilters" class="form-select">
                        <option value="default">По умолчанию</option>
                        <option value="date_desc">По дате (убывание)</option>
                        <option value="date_asc">По дате (возрастание)</option>
                    </select>
                    <button class="btn btn-outline-secondary ms-2" @click="reset">
                        <i class="fas fa-undo-alt"></i>
                    </button>
                </div>
            </div>

            <!-- Сетка постов -->
            <div v-if="loading" class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Загрузка...</span>
                </div>
            </div>
            <div v-else class="row g-4">
                <div class="col-sm-6 col-lg-4" v-for="post in filteredPosts" :key="post.id">
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
                            <small v-if="post.created_at" class="text-muted mb-2">
                                <i class="fas fa-calendar-alt me-1"></i>{{ formatDate(post.created_at) }}
                            </small>
                            <h5 class="card-title fw-bold">{{ post.title }}</h5>
                            <p v-if="post.excerpt" class="text-muted small flex-grow-1">{{ post.excerpt }}</p>
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

            <!-- Пагинация -->
            <nav v-if="pagination.last_page > 1" class="d-flex justify-content-center mt-4">
                <ul class="pagination">
                    <li class="page-item" :class="{ disabled: pagination.current_page === 1 }">
                        <button class="page-link" @click="loadPosts(pagination.current_page - 1)">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                    </li>
                    <li
                        v-for="page in pagination.last_page"
                        :key="page"
                        class="page-item"
                        :class="{ active: page === pagination.current_page }"
                    >
                        <button class="page-link" @click="loadPosts(page)">{{ page }}</button>
                    </li>
                    <li class="page-item" :class="{ disabled: pagination.current_page === pagination.last_page }">
                        <button class="page-link" @click="loadPosts(pagination.current_page + 1)">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </li>
                </ul>
            </nav>
        </div>
    </section>
</template>

<script>
import axios from "axios";
import API_ENDPOINTS from '@/services/api.js';

export default {
    name: "Posts",

    data() {
        return {
            noImage: '/images/no-image.png',
            posts: [],
            loading: true,
            filteredPosts: [],
            filters: {
                keyword: "",
                sortOrder: "default",
            },
            pagination: {
                current_page: 1,
                last_page: 1,
                total: 0,
            },
        };
    },

    methods: {
        formatDate(dateStr) {
            const d = new Date(dateStr);
            return d.toLocaleDateString('ru-RU', { day: 'numeric', month: 'long', year: 'numeric' });
        },
        applyFilters() {
            this.loadPosts(1);
        },

        reset() {
            this.filters = {
                keyword: "",
                sortOrder: "default",
            };
            this.pagination = {
                current_page: 1,
                last_page: 1,
                total: 0,
            };
            this.applyFilters();
        },

        loadPosts(page = 1) {
            this.loading = true;

            const params = {
                page: page,
                keyword: this.filters.keyword || null,
                sort: this.filters.sortOrder || null,
            };

            axios
                .get(API_ENDPOINTS.posts, { params })
                .then((response) => {
                    this.posts = response.data.data;
                    this.filteredPosts = [...this.posts];
                    this.pagination = {
                        current_page: response.data.meta.current_page,
                        last_page: response.data.meta.last_page,
                        total: response.data.meta.total,
                    };
                    window.scrollTo(0, 0);
                })
                .catch((error) => {
                    console.error("Ошибка при загрузке постов:", error);
                })
                .finally(() => {
                    this.loading = false;
                });
        },
    },

    mounted() {
        this.loadPosts();
    },
};
</script>

<style scoped>
.pagination .page-link {
    cursor: pointer;
}
</style>
