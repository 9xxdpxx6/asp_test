<template>
    <section v-if="advantages.length" class="section-spacing bg-white">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-6 section-title">Наши преимущества</h2>
            </div>
            <div class="row g-4 mb-4" v-for="(advantage, index) in advantages" :key="advantage.id || index">
                <div class="col-md-6" :class="{ 'order-md-2': imageOnRight(advantage) }">
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
                <div class="col-md-6 d-flex align-items-center" :class="{ 'order-md-1': imageOnRight(advantage) }">
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
import axios from 'axios';
import API_ENDPOINTS from '@/services/api';

const fallbackAdvantages = [
    {
        title: 'Опытные инструкторы и большой автодром',
        description: 'Сертифицированные инструкторы с многолетним стажем обучают на собственном большом автодроме. Нет очередей, каждое занятие максимально результативно.',
        image: '/images/advantages/advantage-1-voditel.jpg',
        image_on_left: true,
    },
    {
        title: 'Современный автопарк',
        description: 'Обучение на автомобилях с механической и автоматической трансмиссией. Все машины регулярно проходят техобслуживание для вашей безопасности.',
        image: '/images/advantages/advantage-2-car.jpg',
        position: 'center 70%',
        image_on_left: false,
    },
    {
        title: 'Гибкий график обучения',
        description: 'Практика проходит в светлое время суток с 8:00 до 17:00. Теоретические занятия проводятся вечером, чтобы обучение можно было совмещать с работой.',
        image: '/images/advantages/advantage-3-class.JPG',
        image_on_left: true,
    },
];

const imagePositions = {
    'advantage-2-car.jpg': 'center 70%',
};

function withImagePosition(advantage, index) {
    const image = advantage.image || '';
    const matchedEntry = Object.entries(imagePositions).find(([fileName]) => image.endsWith(fileName));
    const imageOnLeft = typeof advantage.image_on_left === 'boolean'
        ? advantage.image_on_left
        : index % 2 === 0;

    return {
        ...advantage,
        image_on_left: imageOnLeft,
        position: advantage.position || matchedEntry?.[1] || null,
    };
}

export default {
    name: 'Advantages',
    data() {
        return {
            advantages: fallbackAdvantages.map((a, i) => withImagePosition(a, i)),
        };
    },
    methods: {
        imageOnRight(advantage) {
            if (typeof advantage.image_on_left === 'boolean') {
                return !advantage.image_on_left;
            }

            return false;
        },
    },
    mounted() {
        axios.get(API_ENDPOINTS.advantages)
            .then((response) => {
                const data = Array.isArray(response.data?.data) ? response.data.data : [];
                this.advantages = data.map((a, i) => withImagePosition(a, i));
            })
            .catch((error) => {
                console.error('Ошибка при загрузке преимуществ:', error);
            });
    },
};
</script>

<style scoped>
</style>
