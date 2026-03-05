<template>
    <div>
        <HeroSection/>
        <TrustBar/>
        <PricingPreview/>
        <WhyChooseUs/>
        <LearningProcess/>
        <Advantages/>
        <Discounts/>
        <ReviewsWidget/>
        <CtaSection/>
        <Blog/>
    </div>
</template>

<script>
import HeroSection from "@/components/Home/HeroSection.vue";
import TrustBar from "@/components/Home/TrustBar.vue";
import PricingPreview from "@/components/Home/PricingPreview.vue";
import WhyChooseUs from "@/components/Home/WhyChooseUs.vue";
import LearningProcess from "@/components/Home/LearningProcess.vue";
import Advantages from "@/components/Home/Advantages.vue";
import Discounts from "@/components/Home/Discounts.vue";
import ReviewsWidget from "@/components/Home/ReviewsWidget.vue";
import CtaSection from "@/components/Home/CtaSection.vue";
import Blog from "@/components/Home/Blog.vue";

export default {
    name: 'Home',
    components: {
        HeroSection,
        TrustBar,
        PricingPreview,
        WhyChooseUs,
        LearningProcess,
        Advantages,
        Discounts,
        ReviewsWidget,
        CtaSection,
        Blog,
    },
};
</script>

<style scoped>
</style>
