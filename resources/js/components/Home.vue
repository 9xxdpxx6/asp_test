<template>
    <div>
        <HeroSection :settings="hero" />
        <TrustBar :items="trustItems" />
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
import axios from 'axios';
import API_ENDPOINTS from '@/services/api';
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
    data() {
        return {
            /** С сервера на главной (layouts/main), чтобы не мигать FALLBACK до ответа API */
            hero:
                typeof window !== 'undefined' && window.__INITIAL_HERO__ != null
                    ? window.__INITIAL_HERO__
                    : null,
        };
    },
    computed: {
        trustItems() {
            return this.hero?.trust_items ?? null;
        },
    },
    mounted() {
        if (this.hero != null) {
            return;
        }
        axios
            .get(API_ENDPOINTS.hero)
            .then((response) => {
                this.hero = response.data?.data ?? null;
            })
            .catch(() => {
                this.hero = null;
            });
    },
};
</script>

<style scoped>
</style>
