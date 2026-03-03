<template>
    <div class="container-fluid mt-5">
        <div class="row align-items-center mb-5 bg-info">
            <!-- Первая колонка: изображение как фон -->
            <div class="col mb-2 mb-md-0 px-0">
                <div class="image-container rounded-0" :style="{ backgroundImage: `url(${discount1.image})` }">
                    <div class="overlay">
                        <h3 class="display-6 text-light">{{ discount1.name }}</h3>
                        <p class="lead text-light">{{ discount1.description }}</p>
                    </div>
                </div>
            </div>

            <div class="col-md-3 text-center mb-2 mb-md-0">
                <div class="text-overlay text-light">
                    <h3 class="display-6">{{ discount2.name }}</h3>
                    <p class="lead">{{ discount3.description }}</p>
                    <router-link :to="{ name: 'discounts' }" class="btn btn-light">
                        <p class="lead my-0">Узнайте больше</p>
                    </router-link>
                </div>
            </div>

            <!-- Третья колонка: изображение как фон -->
            <div class="col mb-2 mb-md-0 px-0">
                <div class="image-container rounded-0" :style="{ backgroundImage: `url(${discount2.image})` }">
                    <div class="overlay">
                        <h3 class="display-6 text-light">{{ discount3.name }}</h3>
                        <p class="lead text-light">{{ discount3.description }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'Discounts',
    data() {
        return {
            discount1: {
                name: 'Cкидки учащимся и студентам РФ',
                description: 'Воспользуйтесь скидкой при получении образования.',
                image: '/images/discounts/discount-left.JPG',
            },
            discount2: {
                name: 'Скидка на водительскую медицинскую комиссию',
                description: 'Скидка в водительской медицинской комиссии «ТехМедЭксперт 1-6-8».',
                image: '/images/discounts/discount-right.jpg',
            },
            discount3: {
                name: 'Оплата материнским капиталом',
                description: 'Используйте материнский капитал для обучения в автошколе.',
                image: '/images/discounts/discount-right.jpg',
            },
        };
    },
};
</script>

<style scoped>
.discounts-container {
    margin: 0 auto;
}

.image-container {
    height: 400px;
    background-size: cover;
    background-position: center;
    position: relative;
    border-radius: 8px;
    overflow: hidden;
}

.overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    background: linear-gradient(
        to bottom,
        rgba(0, 0, 0, 0.55),
        rgba(0, 0, 0, 0.55)
    );
    color: white;
    padding: 20px;
    text-align: center;
}

.overlay h3,
.overlay p {
    text-shadow: 0 2px 8px rgba(0, 0, 0, 0.55);
}

.middle-button {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 400px;
}
</style>


