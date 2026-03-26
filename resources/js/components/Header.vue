<template>
    <header>
        <nav class="navbar fixed-top navbar-expand-lg lead navbar-dark bg-primary">
            <div class="container">
                <!-- Логотип -->
                <router-link class="navbar-brand" :to="{ name: 'home' }" @click="closeNavbar">
                    <img :src="logo" alt="Logo" height="40" />
                </router-link>

                <button
                    class="navbar-toggler"
                    type="button"
                    @click="toggleNavbar"
                    aria-controls="navbarNav"
                    :aria-expanded="isNavbarOpen.toString()"
                    aria-label="Toggle navigation"
                >
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Ссылки на страницы -->
                <div :class="['collapse', 'navbar-collapse', { 'show': isNavbarOpen }]" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <router-link class="nav-link" active-class="active" :to="{ name: 'about' }" @click="closeNavbar">О нас</router-link>
                        </li>
                        <li class="nav-item">
                            <router-link class="nav-link" active-class="active" :to="{ name: 'prices' }" @click="closeNavbar">Цены</router-link>
                        </li>
                        <li class="nav-item">
                            <router-link class="nav-link" active-class="active" :to="{ name: 'discounts' }" @click="closeNavbar">Программы лояльности</router-link>
                        </li>
                        <li class="nav-item">
                            <router-link class="nav-link" active-class="active" :to="{ name: 'blog' }" @click="closeNavbar">Новости</router-link>
                        </li>
                        <li class="nav-item">
                            <router-link class="nav-link" active-class="active" :to="{ name: 'contacts' }" @click="closeNavbar">Контакты</router-link>
                        </li>
                    </ul>

                    <!-- Кнопка обратного звонка -->
                    <button class="btn btn-outline-light" @click="openModal">
                        <span>Обратный звонок</span>
                    </button>
                </div>
            </div>
        </nav>

        <CallbackForm
            v-if="isModalOpen"
            @close="closeModal"
        />
    </header>
</template>

<script>
import CallbackForm from '@/components/CallbackForm.vue';

export default {
    name: 'Header',
    components: { CallbackForm },
    watch: {
        $route() {
            this.closeNavbar();
        },
    },
    data() {
        return {
            logo: '/logo.png',
            isModalOpen: false,
            isNavbarOpen: false,
        };
    },
    methods: {
        openModal() {
            this.closeNavbar();
            this.isModalOpen = true;
        },
        closeModal() {
            this.isModalOpen = false;
        },
        closeNavbar() {
            this.isNavbarOpen = false;
        },
        toggleNavbar() {
            this.isNavbarOpen = !this.isNavbarOpen;
        },
    },
};
</script>

<style scoped>
.navbar-brand img {
    max-height: 40px;
}

.nav-link {
    color: rgba(255, 255, 255, 0.8) !important;
    transition: color 0.2s ease;
}

.nav-link.active,
.nav-link:hover {
    color: #fff !important;
    font-weight: 600;
}
</style>
