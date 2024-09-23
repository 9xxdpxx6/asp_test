<template>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Категории обучения</h2>
        <div class="row">
            <div class="col-md-4" v-for="category in categories" :key="category.id">
                <div class="card h-100 shadow-sm">
                    <img :src="category.image" class="card-img-top" alt="category image">
                    <div class="card-body">
                        <h5 class="card-title">{{ category.title }}</h5>
                        <p class="card-text">{{ category.description }}</p>
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
                { id: 1, title: 'Category A', description: 'Motorcycle driving', image: 'https://via.placeholder.com/600x400.png/ffc107/333?text=Category+A' },
                { id: 2, title: 'Category B', description: 'Car driving', image: 'https://via.placeholder.com/600x400.png/28a745/333?text=Category+B' },
                { id: 3, title: 'Category C', description: 'Truck driving', image: 'https://via.placeholder.com/600x400.png/17a2b8/333?text=Category+C' },
            ],
        };
    },
};
</script>
