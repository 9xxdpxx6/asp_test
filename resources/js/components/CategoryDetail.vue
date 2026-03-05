<template>
    <div class="container my-5">
        <div v-if="loading" class="text-center w-100">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Загрузка...</span>
            </div>
        </div>
        <div v-else-if="category">
            <Breadcrumbs :crumbs="breadcrumbs" />

            <!-- Верхний блок с названием и ценой -->
            <div class="row align-items-center mb-4">
                <div class="col-md-8">                    <div class="d-flex flex-row align-items-start">
                        <div class="flex-grow-1">
                            <h2 class="display-5 mb-3" v-html="formatCategoryName(category.name)"></h2>
                            <p class="fs-4">
                                <span class="badge bg-primary fs-5 me-2">{{ formatPrice(category.price) }} ₽</span>
                            </p>
                        </div>
                        <div v-if="category.icon" class="icon-container ms-auto display-4 text-primary">
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
                <button class="btn btn-primary btn-lg rounded-pill px-5 mb-2" @click="openModal(category)">
                    <i class="fas fa-pen me-2"></i>Записаться
                </button>
            </div>
        </div>
        <div v-else>
            <p class="text-center">Категория не найдена.</p>
        </div>

        <CallbackForm
            v-if="isModalOpen"
            :category="selectedCategory"
            @close="closeModal"
        />
    </div>
</template>

<script>
import axios from 'axios';
import API_ENDPOINTS from '@/services/api';
import DOMPurify from 'dompurify';
import BlockRenderer from '@/components/blocks/BlockRenderer.vue';
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import CallbackForm from '@/components/CallbackForm.vue';
import { formatCategoryName } from '@/utils/formatCategoryName';

export default {
    name: 'CategoryDetail',
    components: {
        BlockRenderer,
        Breadcrumbs,
        CallbackForm,
    },

    data() {
        return {
            category: null,
            loading: true,
            isModalOpen: false,
            selectedCategory: null,
        };
    },

    methods: {
        formatCategoryName,
        formatPrice(price) {
            return Math.floor(price).toLocaleString('ru-RU');
        },
        openModal(category) {
            this.selectedCategory = category;
            this.isModalOpen = true;
        },
        closeModal() {
            this.isModalOpen = false;
            this.selectedCategory = null;
        },
    },

    mounted() {
        axios.get(API_ENDPOINTS.categoryDetails(this.$route.params.id))
            .then(response => {
                this.category = response.data.data;
            })
            .catch(error => {
                console.error('Ошибка при загрузке категории:', error);
            })
            .finally(() => {
                this.loading = false;

                this.$nextTick(() => {
                    const images = document.querySelectorAll('.category-description img');
                    images.forEach(img => {
                        img.onload = () => {
                            const containerWidth = img.parentElement.offsetWidth;
                            if (img.naturalWidth > containerWidth) {
                                img.style.maxWidth = '100%';
                                img.style.height = 'auto';
                            }
                        };
                    });
                });
            });
    },

    computed: {
        safeDescription() {
            return this.category ? DOMPurify.sanitize(this.category.description) : '';
        },
        sortedBlocks() {
            if (!this.category || !this.category.blocks) return [];
            return [...this.category.blocks].sort((a, b) => a.sort_order - b.sort_order);
        },
        breadcrumbs() {
            const crumbs = [
                { label: 'Цены', to: { name: 'prices' } },
            ];
            if (this.category) {
                crumbs.push({ label: this.category.name, to: null });
            }
            return crumbs;
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

.category-marker {
    font-weight: 800;
}
</style>
