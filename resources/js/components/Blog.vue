<template>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Новости</h1>

        <!-- Фильтры и сортировка -->
        <div class="row mb-3">
            <div class="col-12 col-md-6 mb-3">
                <input
                    v-model="filters.keyword"
                    @input="applyFilters"
                    type="text"
                    class="form-control"
                    placeholder="Поиск..."
                />
            </div>
            <div class="col-12 col-md-6 mb-3 d-flex flex-row ">
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
        <div v-if="loading" class="text-center w-100">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Загрузка...</span>
            </div>
        </div>
        <div v-else class="row">
            <div class="mb-4 col-md-3" v-for="post in filteredPosts" :key="post.id">
                <div class="card h-100">
                    <img :src="post.preview" class="card-img-top post-image" alt="post image">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title h3">{{ post.title }}</h5>
                        <router-link :to="{ name: 'post', params: { id: post.id } }" class="btn btn-outline-primary mt-auto">
                            Читать больше
                        </router-link>
                    </div>
                </div>
            </div>
        </div>

        <!-- Пагинация -->
        <nav v-if="pagination.last_page > 1" class="d-flex text-center justify-content-center mt-4 mb-2">
            <ul class="pagination mx-auto">
                <li
                    class="page-item"
                    :class="{ disabled: pagination.current_page === 1 }"
                    @click="loadPosts(pagination.current_page - 1)"
                >
                    <button class="page-link"><i class="fas fa-chevron-left"></i></button>
                </li>
                <li
                    v-for="page in pagination.last_page"
                    :key="page"
                    class="page-item"
                    :class="{ active: page === pagination.current_page }"
                    @click="loadPosts(page)"
                >
                    <button class="page-link">{{ page }}</button>
                </li>
                <li
                    class="page-item"
                    :class="{ disabled: pagination.current_page === pagination.last_page }"
                    @click="loadPosts(pagination.current_page + 1)"
                >
                    <button class="page-link">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </li>
            </ul>
        </nav>
    </div>
</template>

<script>
import axios from "axios"
import API_ENDPOINTS from "@/services/api.js"
import {applyStyles} from "@popperjs/core";

export default {
    name: "Posts",

    data() {
        return {
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
        }
    },

    methods: {
        applyFilters() {
            this.loadPosts(1);
        },

        reset() {
            this.filters = {
                keyword: "",
                sortOrder: "default",
            }
            this.pagination = {
                current_page: 1,
                last_page: 1,
                total: 0,
            }
            this.applyFilters()
        },

        loadPosts(page = 1) {
            this.loading = true

            const params = {
                page: page,
                keyword: this.filters.keyword || null,
                sort: this.filters.sortOrder || null,
            }

            axios
                .get(API_ENDPOINTS.posts, { params })
                .then((response) => {
                    this.posts = response.data.data
                    this.filteredPosts = [...this.posts]
                    this.pagination = {
                        current_page: response.data.meta.current_page,
                        last_page: response.data.meta.last_page,
                        total: response.data.meta.total,
                    }
                    window.scrollTo(0, 0)
                })
                .catch((error) => {
                    console.error("Ошибка при загрузке постов:", error)
                })
                .finally(() => {
                    this.loading = false
                })
        },
    },

    mounted() {
        this.loadPosts()
    },
}
</script>

<style scoped>
.post-image {
    height: 200px;
    object-fit: cover;
    width: 100%;
}
.card {
    transition: transform 0.2s;
}
.card:hover {
    transform: scale(1.05);
}
.pagination .page-link {
    cursor: pointer;
}
</style>
