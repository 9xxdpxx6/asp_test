<template>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Программы лояльности</h1>

        <div v-if="loading" class="text-center w-100">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Загрузка...</span>
            </div>
        </div>
        <div v-else-if="discounts.length > 0" class="row">
            <div class="mb-4 col-md-3" v-for="discount in discounts" :key="discount.id">
                <div class="card h-100">
                    <img v-if="discount.preview" :src="discount.preview" class="card-img-top discount-image" alt="discount image">
                    <img v-else :src="noImage" class="card-img-top post-image" alt="post image">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title h3">{{ discount.title }}</h5>
                        <router-link :to="{ name: 'discount', params: { id: discount.id } }" class="btn btn-outline-primary mt-auto">
                            Читать больше
                        </router-link>
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
import API_ENDPOINTS from '~/services/api.js'
import {applyStyles} from "~popperjs/core";

export default {
    name: "Discount",

    data() {
        return {
            noImage: '/images/no-image.png',
            discounts: [],
            loading: true,
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

    },

    mounted() {
        axios.get(API_ENDPOINTS.discounts)
            .then(response => {
                this.discounts = response.data.data
            })
            .catch(error => {
                console.error('Ошибка при загрузке программ лоядбности:', error)
            })
            .finally(() => {
                this.loading = false
            })
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
