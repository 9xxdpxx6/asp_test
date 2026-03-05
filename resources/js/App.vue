<template>
    <div class="d-flex flex-column min-vh-100">
        <Header/>
        <div class="container-fluid flex-grow-1 p-0 mt-5 pt-4 bg-light">
            <router-view v-slot="{ Component }">
                <transition name="page-fade" mode="out-in">
                    <component :is="Component" />
                </transition>
            </router-view>
        </div>
        <Footer/>

        <!-- Sticky phone FAB for mobile -->
        <a href="tel:+79615262359" class="phone-fab d-md-none" aria-label="Позвонить">
            <i class="fas fa-phone"></i>
        </a>
    </div>
</template>

<script>
import Header from "@/components/Header.vue";
import Footer from "@/components/Footer.vue";

export default {
    name: 'App',
    components: {
        Header,
        Footer,
    },
};
</script>

<style>
/* Quill editor alignment (not provided by Bootstrap) */
.ql-align-center {
    text-align: center !important;
}

.ql-align-right {
    text-align: right !important;
}

.ql-align-justify {
    text-align: justify !important;
}

/* Page transition */
.page-fade-enter-active,
.page-fade-leave-active {
    transition: opacity 0.25s ease;
}
.page-fade-enter-from,
.page-fade-leave-to {
    opacity: 0;
}
</style>
