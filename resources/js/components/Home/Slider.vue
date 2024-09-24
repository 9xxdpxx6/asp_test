<template>
    <div>
        <div id="homeSlider" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active" v-for="(slide, index) in slides" :key="index">
                    <div class="carousel-bg" :style="{ backgroundImage: `url(${slide.image})` }">
                        <div class="carousel-caption">
                            <h3>{{ slide.title }}</h3>
                            <p>{{ slide.description }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#homeSlider" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#homeSlider" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>

        <!-- Rest of the page content here... -->
    </div>
</template>

<script>
export default {
    name: 'Slider',
    data() {
        return {
            slides: [
                { image: 'https://via.placeholder.com/1200x400.png/ff7f7f/333?text=Slider+Image+1', title: 'Discounts Available', description: 'Get up to 20% off on driving courses' },
                { image: 'https://via.placeholder.com/1200x400.png/7f7fff/333?text=Slider+Image+2', title: 'Best Instructors', description: 'Learn from the best professionals' },
                { image: 'https://via.placeholder.com/1200x400.png/7fff7f/333?text=Slider+Image+3', title: 'Achievements', description: 'Our students have a 98% pass rate' },
            ],
        };
    },
};
</script>

<style scoped>
.carousel-bg {
    height: 400px;
    background-size: cover;
    background-position: center;
}
.carousel-caption {
    background-color: rgba(0, 0, 0, 0.5);
    padding: 20px;
}
</style>
