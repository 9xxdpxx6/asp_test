<template>
    <div class="container mt-5">
        <h2 class="text-center mb-4 display-4">Категории обучения</h2>
        <div class="row">
            <div class="col-md-4 mb-2" v-for="category in categories" :key="category.id">
                <div class="card h-100 shadow-sm">
                    <img :src="category.image" class="card-img-top category-image" alt="category image">
                    <div class="card-body">
                        <h5 class="card-title display-6">{{ category.title }}</h5>
                        <p class="card-text lead">{{ category.description }}</p>
                        <!-- Кнопка для перехода к категории -->
                        <div class="d-flex justify-content-between">
                            <h4>{{ category.price }} руб</h4>
                            <div>
                                <router-link :to="{ name: 'category', params: { id: category.id } }" class="btn btn-primary">
                                    Подробнее
                                </router-link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'Categories',
    data() {
        return {
            categories: [
                { id: 1, title: 'Категория A', description: 'Обучение на мотоцикле', image: '/images/category-a-moto.jpeg', price: 50000 },
                { id: 2, title: 'Категория B', description: 'Обучение на легковом автомобиле', image: '/images/category-b-car.jpeg', price: 70000 },
                { id: 3, title: 'Категория C', description: 'Обучение на грузовом автомобиле', image: '/images/category-c-truck.jpeg', price: 90000 },
            ],
        };
    },
};
</script>

<style scoped>
.category-image {
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
