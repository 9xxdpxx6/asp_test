<template>
    <header>
        <nav class="navbar fixed-top navbar-expand-lg lead navbar-info bg-info">
            <div class="container">
                <!-- Логотип -->
                <router-link class="navbar-brand" :to="{ name: 'home' }">
                    <img :src="logo" alt="Logo" height="40" />
                </router-link>

                <button
                    class="navbar-toggler"
                    type="button"
                    @click="toggleNavbar"
                    aria-controls="navbarNav"
                    aria-expanded="isNavbarOpen.toString()"
                    aria-label="Toggle navigation"
                >
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Ссылки на страницы -->
                <div :class="['collapse', 'navbar-collapse', { 'show': isNavbarOpen }]" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <router-link class="nav-link" :to="{ name: 'prices' }">Цены</router-link>
                        </li>
                        <li class="nav-item">
                            <router-link class="nav-link" :to="{ name: 'discounts' }">Скидки</router-link>
                        </li>
                        <li class="nav-item">
                            <router-link class="nav-link" :to="{ name: 'blog' }">Новости</router-link>
                        </li>
<!--                        <li class="nav-item">-->
<!--                            <router-link class="nav-link" :to="{ name: 'about' }">О нас</router-link>-->
<!--                        </li>-->
                        <li class="nav-item">
                            <router-link class="nav-link" :to="{ name: 'contacts' }">Контакты</router-link>
                        </li>
                    </ul>

                    <!-- Кнопка обратного звонка -->
                    <button class="btn btn-outline-light" @click="openModal">
                        <p class="lead my-0">Обратный звонок</p>
                    </button>
                </div>
            </div>
        </nav>

        <CallbackForm
            :isModalOpen="isModalOpen"
            @close="closeModal"
        />
    </header>
</template>

<script>
import axios from 'axios';
import API_ENDPOINTS from '@/services/api.js';
import CallbackForm from "@/components/CallbackForm.vue";

export default {
    components: {
        CallbackForm,
    },
    data() {
        return {
            logo: '/logo.png',
            isModalOpen: false,
            isSubmitted: false,
            form: {
                full_name: '',
                phone: '',
                email: '',
                comment: ''
            },
            isNavbarOpen: false // добавляем состояние для меню
        };
    },
    methods: {
        openModal() {
            this.isModalOpen = true;
        },
        closeModal() {
            this.isModalOpen = false;
            this.isSubmitted = false; // Сбрасываем состояние успешной отправки
            this.resetForm(); // Сбрасываем данные формы
        },
        resetForm() {
            this.form = {
                full_name: '',
                phone: '',
                email: '',
                comment: ''
            };
        },
        submitCallbackRequest() {
            axios.post(API_ENDPOINTS.callbackRequests, this.form)
                .then(response => {
                    this.isSubmitted = true; // Устанавливаем состояние успешной отправки
                    setTimeout(() => {
                        this.closeModal(); // Закрываем модалку через 3 секунды
                        this.isSubmitted = false; // Сбрасываем состояние
                    }, 2000);
                })
                .catch(error => {
                    console.error('Ошибка при отправке запроса:', error);
                });
        },
        // Метод для переключения состояния меню
        toggleNavbar() {
            this.isNavbarOpen = !this.isNavbarOpen;
        }
    }
};
</script>

<style scoped>
.navbar-brand img {
    max-height: 40px;
}
.nav-link {
    color: white !important;
}

/* Стили для модалки */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
}

.modal-content {
    background: white;
    padding: 20px;
    border-radius: 8px;
    width: 400px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}

.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}

/* Анимация модального окна */
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease, transform 0.3s ease;
}
.fade-enter,
.fade-leave-to {
    opacity: 0;
    transform: scale(0.9);
}
</style>
