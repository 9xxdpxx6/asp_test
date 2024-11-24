<template>
    <div class="container mt-5">
        <h2 class="text-center display-4 mb-4">Программы лояльности</h2>

        <div v-if="loading" class="text-center w-100">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Загрузка...</span>
            </div>
        </div>
        <div v-else-if="discounts.length > 0" class="row">
            <div class="mb-4 col-md-3" v-for="discount in filteredPosts" :key="discount.id">
                <div class="card h-100">
                    <img :src="discount.preview" class="card-img-top discount-image" alt="discount image">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title h3">{{ discount.title }}</h5>
                        <a :href="'/blog/' + discount.id" class="btn btn-outline-primary mt-auto">Читать больше</a>
                    </div>
                </div>
            </div>
        </div>
        <div v-else class="row text-center">
            <h3>На данный момент программы лояльности отсутствуют</h3>
        </div>
    </div>
</template>

<script>
import axios from "axios"
import API_ENDPOINTS from "@/services/api.js"
import {applyStyles} from "@popperjs/core";

export default {
    name: "Discount",

    data() {
        return {
            discounts: [],
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
            this.loadDiscounts(1);
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

        loadDiscounts(page = 1) {
            this.loading = true

            const params = {
                page: page,
                keyword: this.filters.keyword || null,
                sort: this.filters.sortOrder || null,
            }

            axios
                .get(API_ENDPOINTS.discounts, { params })
                .then((response) => {
                    this.discounts = response.data.data
                    console.log(this.discounts);
                })
                .catch((error) => {
                    console.error("Ошибка при загрузке скидок:", error)
                })
                .finally(() => {
                    this.loading = false
                })
        },
    },

    mounted() {
        this.loadDiscounts()
    },
}
</script>

<style scoped>
.discount-image {
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
