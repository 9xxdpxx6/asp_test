<template>
    <Header></Header>
    <div class="container-fluid mt-5 pt-4 bg-light">
        <router-view></router-view>
    </div>
</template>

<script>
import Header from "@/components/Header.vue";

export default {
    name: 'App',
    components: { Header },
};
</script>

<style scoped>
</style>
