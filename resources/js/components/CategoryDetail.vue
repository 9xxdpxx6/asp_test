<template>
    <div class="container my-5">
        <div v-if="loading" class="text-center w-100">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Загрузка...</span>
            </div>
        </div>
        <div v-else-if="category">
            <!-- Верхний блок с названием, превью, длительностью и ценой -->
            <div class="row align-items-center mb-4">
                <div class="col-md-8 lead">
                    <div class="d-flex flex-row">
                        <h2 class="display-4 mb-3 w-75">{{ category.name }}</h2>
                        <div class="icon-container ms-auto display-4">
                            <i :class="category.icon"></i>
                        </div>
                    </div>
                    <p class="mb-2"><strong class="fw-bold">Длительность:</strong> {{ category.duration }} часов</p>
                    <p><strong class="fw-bold">Цена:</strong> {{ Math.floor(category.price) }} руб.</p>
                </div>
            </div>

            <!-- Описание на всю ширину контейнера -->
            <div>
                <h4 class="mb-3">Описание:</h4>
                <p class="lead category-description text-wrap" v-html="safeDescription"></p>
            </div>

            <!-- Кнопка для возврата -->
            <div class="text-center mt-4">
                <button class="btn btn-primary px-5 mb-2" @click="openModal(category)">
                    Записаться
                </button>
            </div>
        </div>
        <div v-else>
            <p class="text-center">Категория не найдена.</p>
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
                                <input type="text" class="form-control" id="full_name" v-model="form.full_name" required>
                            </div>
                            <div class="mb-3">
                                <label for="phone" class="form-label">Телефон</label>
                                <input type="tel" class="form-control" id="phone" v-model="form.phone" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Почта</label>
                                <input type="email" class="form-control" id="email" v-model="form.email">
                            </div>
                            <div class="mb-3">
                                <label for="comment" class="form-label">Комментарий</label>
                                <textarea class="form-control" id="comment" v-model="form.comment"></textarea>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" @click="closeModal">Отмена</button>
                                <button type="submit" class="btn btn-primary">Отправить</button>
                            </div>
                        </form>
                    </template>
                </div>
            </div>
        </transition>
    </div>
</template>

<script>
import axios from 'axios';
import API_ENDPOINTS from '~/services/api';
import DOMPurify from "dompurify";

export default {
    name: 'CategoryDetail',

    data() {
        return {
            category: null,
            loading: true,
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
        axios.get(API_ENDPOINTS.categoryDetails(this.$route.params.id))
            .then(response => {
                this.category = response.data.data
            })
            .catch(error => {
                console.error('Ошибка при загрузке категории:', error)
            })
            .finally(() => {
                this.loading = false

                this.$nextTick(() => {
                    const images = document.querySelectorAll('.category-description img')
                    images.forEach(img => {
                        img.onload = () => {
                            const containerWidth = img.parentElement.offsetWidth
                            if (img.naturalWidth > containerWidth) {
                                img.style.maxWidth = '100%' // Ограничиваем ширину
                                img.style.height = 'auto' // Сохраняем пропорции
                            }
                        }
                    })
                })
            })
    },

    computed: {
        safeDescription() {
            // Очищаем описание от потенциально вредного HTML
            return this.category ? DOMPurify.sanitize(this.category.description) : ''
        },
    },
};
</script>

<style>
.category-description img {
    max-width: 100%; /* Изображение не выходит за пределы контейнера */
    height: auto; /* Сохраняет пропорции изображения */
    width: auto; /* Устраняет обрезку при наличии фиксированной ширины */
    display: block; /* Убирает inline-отступы */
    margin: 0 auto; /* Центрирует изображение */
}
.category-description {
    max-width: 100%; /* Контент не выходит за пределы контейнера */
    overflow: hidden; /* Убирает горизонтальную прокрутку */
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
