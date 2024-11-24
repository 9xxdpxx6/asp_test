<template>
    <div>
        <Slider/>
        <Categories/>
        <Discounts/>
        <WhyChooseUs/>
        <Advantages/>
        <Blog/>
    </div>
</template>

<script>
import Slider from "@/components/Home/Slider.vue";
import Categories from "@/components/Home/Categories.vue";
import Discounts from "@/components/Home/Discounts.vue";
import WhyChooseUs from "@/components/Home/WhyChooseUs.vue";
import Advantages from "@/components/Home/Advantages.vue";
import Blog from "@/components/Home/Blog.vue";

export default {
    name: 'Home',
    components: {
        Blog,
        Advantages,
        Discounts,
        WhyChooseUs,
        Categories,
        Slider,
    },
};
</script>

<style scoped>
</style>
