<template>
    <div class="container my-5">
        <div v-if="category">
            <h2 class="text-center mb-4 display-4">{{ category.title }}</h2>
            <div class="row">
                <div class="col-md-6">
                    <img :src="category.image" alt="Category Image" class="img-fluid category-detail-image" />
                </div>
                <div class="col-md-6">
                    <h4 class="mb-3">Описание:</h4>
                    <p class="lead">{{ category.description }}</p>
                    <h4 class="mb-3">Цена: {{ category.price }} руб.</h4>
                    <router-link to="/prices" class="btn btn-primary mt-3">Вернуться к категориям</router-link>
                </div>
            </div>
        </div>
        <div v-else>
            <p class="text-center">Категория не найдена.</p>
        </div>
    </div>
</template>

<script>
export default {
    name: 'CategoryDetail',
    data() {
        return {
            categories: [
                { id: 1, title: 'Категория A', description: 'Обучение на мотоцикле', image: '/images/category-a-moto.jpeg', price: 5000 },
                { id: 2, title: 'Категория B', description: 'Обучение на легковом автомобиле', image: '/images/category-b-car.jpeg', price: 7000 },
                { id: 3, title: 'Категория C', description: 'Обучение на грузовом автомобиле', image: '/images/category-c-truck.jpeg', price: 9000 },
            ],
            category: null,
        };
    },
    mounted() {
        const categoryId = parseInt(this.$route.params.id);
        this.category = this.categories.find(category => category.id === categoryId);
    },
};
</script>

<style scoped>
.category-detail-image {
    max-width: 100%;
    height: auto;
    object-fit: cover;
}
</style>
