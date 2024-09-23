<template>
    <div class="advantages-section mt-5">
        <div class="row mb-5" v-for="(advantage, index) in advantages" :key="index">
            <div class="col-md-6" :class="{'order-md-2': index % 2 !== 0}">
                <img :src="advantage.image" class="img-fluid" alt="advantage image">
            </div>
            <div class="col-md-6" :class="{'order-md-1': index % 2 !== 0}">
                <h3>{{ advantage.title }}</h3>
                <p>{{ advantage.description }}</p>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'Advantages',
    data() {
        return {
            advantages: [
                { title: 'Modern Facilities', description: 'State-of-the-art equipment...', image: 'https://via.placeholder.com/800x400.png/f94d6a/333?text=Advantage+1' },
                { title: 'Experienced Instructors', description: 'Our instructors have...', image: 'https://via.placeholder.com/800x400.png/48c9b0/333?text=Advantage+2' },
            ],
        };
    },
};
</script>
