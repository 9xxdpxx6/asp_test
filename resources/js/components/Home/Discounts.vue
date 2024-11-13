<template>
    <div class="container-fluid mt-5">
        <div class="row align-items-center mb-5 bg-dark">
            <!-- Первая колонка: изображение как фон -->
            <div class="col mb-2 mb-md-0 px-0">
                <div class="image-container rounded-0" :style="{ backgroundImage: `url(${discount1.image})` }">
                    <div class="overlay">
                        <h3 class="display-5 text-light">{{ discount1.name }}</h3> <!-- Увеличенный заголовок -->
                        <p class="lead text-light">{{ discount1.description }}</p> <!-- Увеличенный текст -->
                    </div>
                </div>
            </div>

            <div class="col-md-3 text-center mb-2 mb-md-0">
                <div class="text-overlay text-light">
                    <h3 class="display-5">{{ discount1.name }}</h3> <!-- Увеличенный заголовок -->
                    <p class="lead">{{ discount1.description }}</p> <!-- Увеличенный текст -->
                    <a href="/discounts" class="btn btn-primary">Узнать больше</a>
                </div>
            </div>

            <!-- Третья колонка: изображение как фон -->
            <div class="col mb-2 mb-md-0 px-0">
                <div class="image-container rounded-0" :style="{ backgroundImage: `url(${discount2.image})` }">
                    <div class="overlay">
                        <h3 class="display-5 text-light">{{ discount2.name }}</h3> <!-- Увеличенный заголовок -->
                        <p class="lead text-light">{{ discount2.description }}</p> <!-- Увеличенный текст -->
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
                name: 'Скидка на первые уроки',
                description: 'Получите 20% скидку на первые 5 уроков в нашей автошколе.',
                image: '/images/discounts/discount-1-student.jpg',
            },
            discount2: {
                name: 'Семейные скидки',
                description: 'Семейная скидка на обучение для двух и более членов семьи.',
                image: '/images/discounts/discount-2-like.jpg',
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
    background-color: rgba(0, 0, 0, 0.5); /* Затенение фона */
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
