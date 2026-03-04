<template>
    <div class="container my-5">
        <div v-if="loading" class="text-center w-100">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Загрузка...</span>
            </div>
        </div>
        <div v-else-if="category">
            <!-- Верхний блок с названием и ценой -->
            <div class="row align-items-center mb-4">
                <div class="col-md-8 lead">
                    <div class="d-flex flex-row align-items-start">
                        <div class="flex-grow-1">
                            <h2 class="display-4 mb-3" v-html="formatCategoryName(category.name)"></h2>
                            <p><strong class="fw-bold">Цена:</strong> {{ Math.floor(category.price) }} руб.</p>
                        </div>
                        <div v-if="category.icon" class="icon-container ms-auto display-4">
                            <i :class="category.icon"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Блоки контента -->
            <div v-if="category.blocks && category.blocks.length > 0" class="category-blocks">
                <BlockRenderer
                    v-for="block in sortedBlocks"
                    :key="block.id"
                    :block="block"
                />
            </div>

            <!-- Обратная совместимость: старое описание -->
            <div v-else-if="category.description">
                <div class="lead category-description text-wrap" v-html="safeDescription"></div>
            </div>

            <!-- Кнопка для записи -->
            <div class="text-center mt-5">
                <button class="btn btn-primary btn-lg px-5 mb-2" @click="openModal(category)">
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
                        <h5>Заявка успешно отправлена!</h5>
                        <p>Спасибо за запись. Мы свяжемся с вами в ближайшее время.</p>
                    </template>
                    <template v-else>
                        <h5><span>Запись на </span><span v-html="formatCategoryName(selectedCategory?.name)"></span></h5>
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
import API_ENDPOINTS from '@/services/api';
import DOMPurify from 'dompurify';
import BlockRenderer from '@/components/blocks/BlockRenderer.vue';
import { formatCategoryName } from '@/utils/formatCategoryName';

export default {
    name: 'CategoryDetail',
    components: {
        BlockRenderer,
    },

    data() {
        return {
            category: null,
            loading: true,
            isModalOpen: false,
            isSubmitted: false,
            selectedCategory: null,
            form: {
                full_name: '',
                phone: '',
                email: '',
                comment: ''
            }
        }
    },

    methods: {
        formatCategoryName,
        openModal(category) {
            this.selectedCategory = category
            this.isModalOpen = true
            this.form.comment += `${category?.name}`
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
        submitCallbackRequest() {
            axios.post(API_ENDPOINTS.callbackRequests, this.form)
                .then(response => {
                    this.isSubmitted = true;
                    setTimeout(() => {
                        this.closeModal();
                        this.isSubmitted = false;
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
                                img.style.maxWidth = '100%'
                                img.style.height = 'auto'
                            }
                        }
                    })
                })
            })
    },

    computed: {
        safeDescription() {
            return this.category ? DOMPurify.sanitize(this.category.description) : ''
        },
        sortedBlocks() {
            if (!this.category || !this.category.blocks) return []
            return [...this.category.blocks].sort((a, b) => a.sort_order - b.sort_order)
        },
    },
};
</script>

<style>
.category-description img {
    max-width: 100%;
    height: auto;
    width: auto;
    display: block;
    margin: 0 auto;
}
.category-description {
    max-width: 100%;
    overflow: hidden;
}

.modal-overlay {
    position: fixed;
    top: 0; left: 0; width: 100%; height: 100%;
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
.fade-enter-from, .fade-leave-to {
    opacity: 0;
    transform: scale(0.9);
}

.category-marker {
    font-weight: 800;
}
</style>
