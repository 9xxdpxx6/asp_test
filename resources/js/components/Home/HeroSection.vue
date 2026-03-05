<template>
    <section class="hero-section" :style="{ backgroundImage: `url(${backgroundImage})` }">
        <div class="hero-overlay">
            <div class="container text-center text-white">
                <h1 class="display-4 fw-bold mb-3">Автошкола Политех</h1>
                <p class="lead mb-4 mx-auto" style="max-width: 600px;">
                    Обучение категорий A и B в Краснодаре. Опытные инструкторы, современные автомобили, удобное расписание.
                </p>
                <div class="d-flex justify-content-center gap-3 flex-wrap">
                    <router-link :to="{ name: 'prices' }" class="btn btn-light btn-lg rounded-pill px-4">
                        <i class="fas fa-graduation-cap me-2"></i>Узнать стоимость
                    </router-link>
                    <a href="#pricing-preview" class="btn btn-outline-light btn-lg rounded-pill px-4">
                        <i class="fas fa-phone me-2"></i>Записаться
                    </a>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
export default {
    name: 'HeroSection',
    data() {
        return {
            backgroundImage: '/images/slider/slide-1-cars.JPG',
        };
    },
};
</script>

<style scoped>
.hero-section {
    min-height: 550px;
    background-size: cover;
    background-position: center;
    position: relative;
    display: flex;
    align-items: center;
    margin-top: -10px;
}

.hero-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(
        to bottom,
        rgba(0, 0, 0, 0.45),
        rgba(0, 0, 0, 0.6)
    );
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem 1rem;
}

.hero-section h1 {
    text-shadow: 0 2px 8px rgba(0, 0, 0, 0.4);
}

.hero-section .lead {
    text-shadow: 0 1px 4px rgba(0, 0, 0, 0.3);
    font-size: 1.15rem;
}

@media (max-width: 768px) {
    .hero-section {
        min-height: 400px;
    }
}
</style>
