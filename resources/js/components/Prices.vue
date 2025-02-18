<template>
    <div class="container">
        <h1 class="text-center my-4">Цены на обучение</h1>
        <div v-if="loading" class="text-center w-100">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Загрузка...</span>
            </div>
        </div>
        <div v-else class="row">
            <div class="col-md-4 mb-4" v-for="category in categories" :key="category.id">
                <div class="card h-100 shadow-sm position-relative">
                    <div class="icon-container position-absolute top-0 end-0 p-3 display-3">
                        <i :class="category.icon"></i>
                    </div>
                    <div class="card-body">
                        <h2 class="card-title w-75">{{ category.name }}</h2>
                        <p><strong>Цена:</strong> {{ category.price }} руб.</p>
                        <p><strong>Длительность обучения:</strong> {{ category.duration }} часов.</p>
                    </div>
                    <div class="card-footer bg-transparent border-0">
                        <div class="row">
                            <div class="col-6">
                                <router-link :to="{ name: 'category', params: { id: category.id } }" class="btn btn-outline-primary w-100">
                                    Подробнее
                                </router-link>
                            </div>
                            <div class="col-6">
                                <button class="btn btn-primary w-100 mb-2" @click="openModal(category)">
                                    Записаться
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

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
                        <h5>Запись на {{ selectedCategory?.name }}</h5>
                        <form @submit.prevent="submitCallbackRequest" method="POST">
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
    </div>
</template>

<script>
import axios from "axios";
import API_ENDPOINTS from '@/services/api.js';

export default {
    data() {
        return {
            categories: [],
            loading: true,
            selectedCategory: null,
            isModalOpen: false,
            isSubmitted: false,
            isSubmitting: false,
            form: {
                full_name: '',
                phone: '',
                email: '',
                comment: ''
            }
        }
    },

    methods: {
        openModal(category) {
            this.selectedCategory = category;
            this.isModalOpen = true;
            this.form.comment = `${category?.name}`;
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
        }
    },

    mounted() {
        axios.get(API_ENDPOINTS.categories)
            .then(response => {
                this.categories = response.data.data;
            })
            .catch(error => {
                console.error('Ошибка при загрузке категорий:', error);
            })
            .finally(() => {
                this.loading = false;
            });
    }
}
</script>

<style scoped>
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

.fade-enter-active, .fade-leave-active {
    transition: opacity 0.3s ease, transform 0.3s ease;
}

.fade-enter, .fade-leave-to {
    opacity: 0;
    transform: scale(0.9);
}

.modal-content {
    transform: scale(1);
    animation: pop-in 0.3s ease forwards;
}

@keyframes pop-in {
    from {
        opacity: 0;
        transform: scale(0.9);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}
</style>
