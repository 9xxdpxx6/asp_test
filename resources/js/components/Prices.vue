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
                        <div class="row" >
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

        <CallbackForm
            :isModalOpen="isModalOpen"
            :comment="selectedCategory?.title"
            @close="closeModal"
        />
    </div>
</template>

<script>
import axios from "axios";
import API_ENDPOINTS from "@/services/api.js";
import CallbackForm from "@/components/CallbackForm.vue";

export default {
    components: {
        CallbackForm,
    },
    data() {
        return {
            categories: [],
            loading: true,
            selectedCategory: null,
            isModalOpen: false,
            isSubmitted: false,
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
            this.selectedCategory = category
            this.isModalOpen = true
            this.form.comment += `${category?.name}`
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
        }
    },

    mounted() {
        axios.get(API_ENDPOINTS.categories)
            .then(response => {
                console.log(response);
                this.categories = response.data.data
            })
            .catch(error => {
                console.error('Ошибка при загрузке категорий:', error)
            })
            .finally(() => {
                this.loading = false
            })
    },

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

/* Плавный переход для модального окна */
.fade-enter-active, .fade-leave-active {
    transition: opacity 0.3s ease, transform 0.3s ease;
}

.fade-enter, .fade-leave-to {
    opacity: 0;
    transform: scale(0.9); /* Уменьшение размера при появлении */
}

.modal-content {
    transform: scale(1);
    animation: pop-in 0.3s ease forwards; /* Эффект увеличения при появлении */
}

/* Анимация для модального контента */
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
