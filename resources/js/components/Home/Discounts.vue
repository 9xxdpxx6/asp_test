<template>
    <section class="section-spacing bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-6 section-title">Скидки и акции</h2>
            </div>
            <div class="row g-4 justify-content-center">
                <div class="col-md-6 col-lg-5" v-for="(discount, index) in visibleDiscounts" :key="index">
                    <div class="card h-100 shadow-sm card-hover overflow-hidden">
                        <div class="discount-image-wrapper">
                            <img :src="discount.image" :alt="discount.name" class="img-ratio-3x2" loading="lazy">
                            <div class="discount-overlay">
                                <h5 class="text-white fw-bold mb-2">{{ discount.name }}</h5>
                                <p class="text-white-50 small mb-0">{{ discount.description }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center mt-4">
                <router-link :to="{ name: 'discounts' }" class="btn btn-outline-primary rounded-pill px-4">
                    Узнать о всех скидках <i class="fas fa-arrow-right ms-1"></i>
                </router-link>
            </div>
        </div>
    </section>
</template>

<script>
export default {
    name: 'Discounts',
    data() {
        return {
            discounts: [
                {
                    name: 'Скидки учащимся и студентам РФ',
                    description: 'Воспользуйтесь скидкой при получении образования.',
                    image: '/images/discounts/discount-left.JPG',
                },
                {
                    name: 'Скидка на медкомиссию',
                    description: 'Скидка в водительской медицинской комиссии «ТехМедЭксперт 1-6-8».',
                    image: '/images/discounts/discount-right.jpg',
                },
                {
                    name: 'Оплата материнским капиталом',
                    description: 'Используйте материнский капитал для обучения в автошколе.',
                    image: '/images/discounts/discount-right.jpg',
                },
            ],
        };
    },
    computed: {
        visibleDiscounts() {
            return this.discounts.filter((_, index) => index !== 1);
        },
    },
};
</script>

<style scoped>
.discount-image-wrapper {
    position: relative;
}

.discount-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(to top, rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.2));
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
    padding: 1.25rem;
}
</style>
