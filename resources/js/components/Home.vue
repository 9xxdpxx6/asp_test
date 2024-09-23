<template>
    <div>
        <Slider/>
        <Categories/>
        <Discounts/>
        <Info/>
        <Advantages/>
        <Accordion/>
        <Posts/>
    </div>
</template>

<script>
import Slider from "@/components/Home/Slider.vue";
import Categories from "@/components/Home/Categories.vue";
import Discounts from "@/components/Home/Discounts.vue";
import Info from "@/components/Home/Info.vue";
import Advantages from "@/components/Home/Advantages.vue";
import Accordion from "@/components/Home/Accordion.vue";
import Posts from "@/components/Home/Posts.vue";

export default {
    name: 'Home',
    components: {Posts, Accordion, Advantages, Info, Discounts, Categories, Slider},
};
</script>

<style scoped>
</style>
