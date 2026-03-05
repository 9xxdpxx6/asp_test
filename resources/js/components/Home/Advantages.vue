<template>
    <section class="section-spacing bg-white">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-6 section-title">Наши преимущества</h2>
            </div>
            <div class="row g-4 mb-4" v-for="(advantage, index) in advantages" :key="index">
                <div class="col-md-6" :class="{'order-md-2': index % 2 !== 0}">
                    <div class="rounded-3 overflow-hidden">
                        <img
                            :src="advantage.image"
                            class="img-ratio-3x2 rounded-3"
                            :style="{ objectPosition: advantage.position || 'center center' }"
                            :alt="advantage.title"
                            loading="lazy"
                        >
                    </div>
                </div>
                <div class="col-md-6 d-flex align-items-center" :class="{'order-md-1': index % 2 !== 0}">
                    <div class="p-3">
                        <h4 class="fw-bold mb-3">{{ advantage.title }}</h4>
                        <p class="text-muted">{{ advantage.description }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
export default {
    name: 'Advantages',
    data() {
        return {
            advantages: [
                {
                    title: 'Опытные инструкторы и большой автодром',
                    description: 'Сертифицированные инструкторы с многолетним стажем обучают на собственном большом автодроме. Нет очередей — каждое занятие максимально результативно.',
                    image: '/images/advantages/advantage-1-voditel.jpg'
                },
                {
                    title: 'Современный автопарк',
                    description: 'Обучение на автомобилях с механической и автоматической трансмиссией. Все машины регулярно проходят техобслуживание для вашей безопасности.',
                    image: '/images/advantages/advantage-2-car.jpg',
                    position: 'center 70%'
                },
                {
                    title: 'Гибкий график обучения',
                    description: 'Практика проходит в светлое время суток с 8:00 до 17:00. Теоретические занятия — в вечернее время, чтобы вы могли совмещать с работой.',
                    image: '/images/advantages/advantage-3-class.JPG'
                },
            ],
        };
    },
};
</script>

<style scoped>
</style>
