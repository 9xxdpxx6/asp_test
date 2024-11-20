<template>
    <div class="d-flex flex-column min-vh-100">
        <Header/>
        <div class="container-fluid flex-grow-1 p-0 mt-5 pt-4 bg-light">
            <router-view></router-view>
        </div>
        <Footer/>
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
/* Общие стили для выравнивания футера */
.d-flex {
    display: flex;
    flex-direction: column;
}

.min-vh-100 {
    min-height: 100vh;
}

.flex-grow-1 {
    flex-grow: 1;
}

/* Стили для выравнивания текста в Quill */
.ql-align-center {
    text-align: center !important;
}

.ql-align-right {
    text-align: right !important;
}

.ql-align-justify {
    text-align: justify !important;
}
</style>
