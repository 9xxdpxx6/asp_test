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

        <!-- Модальное окно -->
        <transition name="fade">
            <div v-if="isModalOpen" class="modal-overlay">
                <div class="modal-content">
                    <template v-if="isSubmitted">
                        <!-- Сообщение об успехе -->
                        <h5>Заявка успешно отправлена!</h5>
                        <p>Спасибо за запись. Мы свяжемся с вами в ближайшее время.</p>
                    </template>
                    <template v-else>
                        <!-- Форма записи -->
                        <h5>Запись на Обратный звонок</h5>
                        <form @submit.prevent="submitCallbackRequest">
                            <div class="mb-3">
                                <label for="full_name" class="form-label">ФИО</label>
                                <input type="text" class="form-control" id="full_name" v-model="form.full_name" required :disabled="isSubmitting">
                            </div>
                            <div class="mb-3">
                                <label for="phone" class="form-label">Телефон</label>
                                <input type="tel" class="form-control" id="phone" v-model="form.phone" required :disabled="isSubmitting">
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Почта</label>
                                <input type="email" class="form-control" id="email" v-model="form.email" :disabled="isSubmitting">
                            </div>
                            <div class="mb-3">
                                <label for="comment" class="form-label">Комментарий</label>
                                <textarea class="form-control" id="comment" v-model="form.comment" :disabled="isSubmitting"></textarea>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" @click="closeModal" :disabled="isSubmitting">Отмена</button>
                                <button type="submit" class="btn btn-primary" :disabled="isSubmitting">
                                    {{ isSubmitting ? 'Отправляем...' : 'Отправить' }}
                                </button>
                            </div>
                        </form>
                    </template>
                </div>
            </div>
        </transition>
    </header>
</template>

<script>
import axios from 'axios';
import API_ENDPOINTS from '@/services/api.js';

export default {
    data() {
        return {
            logo: '/logo.png',
            isModalOpen: false,
            isSubmitted: false,
            isSubmitting: false,
            form: {
                full_name: '',
                phone: '',
                email: '',
                comment: ''
            },
            isNavbarOpen: false // состояние для меню
        };
    },
    methods: {
        openModal() {
            this.isModalOpen = true;
        },
        closeModal() {
            this.isModalOpen = false;
            this.isSubmitted = false;
            this.resetForm();
        },
        resetForm() {
            this.form = {
                full_name: '',
                phone: '',
                email: '',
                comment: ''
            };
        },
        async submitCallbackRequest() {
            if (this.isSubmitting) return;

            this.isSubmitting = true;
            try {
                await axios.post(API_ENDPOINTS.callbackRequests, this.form);
                this.isSubmitted = true;
                setTimeout(() => {
                    this.closeModal();
                }, 2000);
            } catch (error) {
                console.error('Ошибка при отправке запроса:', error);
            } finally {
                this.isSubmitting = false;
            }
        },
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
