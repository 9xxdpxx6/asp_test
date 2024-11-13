<template>
    <header>
        <nav class="navbar fixed-top navbar-expand-lg navbar-dark bg-dark">
            <div class="container">
                <!-- Логотип -->
                <router-link class="navbar-brand" to="/">
                    <img :src="logo" alt="Logo" height="40" />
                </router-link>

                <button
                    class="navbar-toggler"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#navbarNav"
                    aria-controls="navbarNav"
                    aria-expanded="false"
                    aria-label="Toggle navigation"
                >
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Ссылки на страницы -->
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <router-link class="nav-link" to="/prices">Цены</router-link>
                        </li>
                        <li class="nav-item">
                            <router-link class="nav-link" to="/about">О нас</router-link>
                        </li>
                        <li class="nav-item">
                            <router-link class="nav-link" to="/contacts">Контакты</router-link>
                        </li>
                    </ul>

                    <!-- Кнопка обратного звонка -->
                    <button class="btn btn-secondary" @click="requestCallback">
                        Обратный звонок
                    </button>
                </div>
            </div>
        </nav>
    </header>
</template>

<script>
export default {
    data() {
        return {
            logo: '/logo.png' // Должно быть в объекте data
        };
    },
    methods: {
        requestCallback() {
            // Логика для вызова обратного звонка
            alert('Запрос на обратный звонок отправлен!');
        },
    },
};
</script>

<style scoped>
.navbar-brand img {
    max-height: 40px;
}
.nav-link {
    color: white !important;
}
</style>
