<template>
    <Header></Header>
    <div class="container-fluid p-0 mt-5 pt-4 bg-light">
        <router-view></router-view>
    </div>
    <Footer/>
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
