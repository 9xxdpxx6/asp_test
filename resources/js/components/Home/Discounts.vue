<template>
    <div class="container-fluid mt-5">
        <div class="row align-items-center mb-5 bg-info">
            <!-- Первая колонка: изображение как фон -->
            <div class="col mb-2 mb-md-0 px-0">
                <div class="image-container rounded-0" :style="{ backgroundImage: `url(${discount1.image})` }">
                    <div class="overlay">
                        <h3 class="display-5 text-light">{{ discount1.name }}</h3>
                        <p class="lead text-light">{{ discount1.description }}</p>
                    </div>
                </div>
            </div>

            <div class="col-md-3 text-center mb-2 mb-md-0">
                <div class="text-overlay text-light">
                    <h3 class="display-5">{{ discount1.name }}</h3>
                    <p class="lead">{{ discount1.description }}</p>
                    <router-link :to="{ name: 'discounts' }" class="btn btn-light">
                        <p class="lead my-0">Узнать больше</p>
                    </router-link>
                </div>
            </div>

            <!-- Третья колонка: изображение как фон -->
            <div class="col mb-2 mb-md-0 px-0">
                <div class="image-container rounded-0" :style="{ backgroundImage: `url(${discount2.image})` }">
                    <div class="overlay">
                        <h3 class="display-5 text-light">{{ discount2.name }}</h3>
                        <p class="lead text-light">{{ discount2.description }}</p>
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
                name: 'Оплата материнским капиталом',
                description: 'Используйте материнский капитал для обучения в автошколе.',
                image: '/images/discounts/discount-1-skoda.JPG',
            },
            discount2: {
                name: 'скидка на водительскую медицинскую комиссию',
                description: 'Скидка в водительской медицинской комиссии «ТехМедЭксперт 1-6-8».',
                image: '/images/discounts/discount-2-class.jpg',
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
    height: 400px; /* Установите высоту для одинакового размера блоков */
    background-size: cover; /* Масштабирование фона */
    background-position: center; /* Центрирование фона */
    position: relative; /* Для позиционирования вложенного блока */
    border-radius: 8px; /* Закругленные углы */
    overflow: hidden; /* Скрытие излишков */
}

.overlay {
    position: absolute; /* Абсолютное позиционирование */
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    display: flex;
    flex-direction: column;
    justify-content: center; /* Центрирование по вертикали */
    align-items: center; /* Центрирование по горизонтали */
    background-color: rgba(0, 0, 0, 0.25); /* Затенение фона */
    color: white; /* Цвет текста */
    padding: 20px; /* Отступы для текста */
    text-align: center; /* Центрирование текста */
}

.middle-button {
    display: flex;
    justify-content: center; /* Центрирование кнопки */
    align-items: center; /* Центрирование кнопки по вертикали */
    height: 400px; /* Высота блока для кнопки */
}
</style>
