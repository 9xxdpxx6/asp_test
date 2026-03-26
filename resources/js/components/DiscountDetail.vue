<template>
    <div class="container my-5">
        <div v-if="loading" class="text-center w-100">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Загрузка...</span>
            </div>
        </div>
        <div v-else-if="discount">
            <Breadcrumbs :crumbs="breadcrumbs" />

            <div class="row align-items-center mb-4">
                <div class="col-md-8 lead">
                    <div class="d-flex flex-row">
                        <h2 class="display-4 mb-3 w-75">{{ discount.name }}</h2>
                    </div>
                    <p v-if="showPercentage" class="mb-2">
                        <strong class="fw-bold">Размер скидки:</strong> {{ discount.percentage }} %
                    </p>
                    <p v-if="discount.excerpt" class="text-muted mb-0">{{ discount.excerpt }}</p>
                </div>
            </div>

            <div v-if="sortedBlocks.length" class="discount-blocks">
                <BlockRenderer
                    v-for="block in sortedBlocks"
                    :key="block.id"
                    :block="block"
                />
            </div>
            <div v-else-if="discount.description">
                <h4 class="mb-3">Описание</h4>
                <p class="lead discount-description text-wrap" v-html="safeDescription"></p>
            </div>

            <div class="text-center mt-4">
                <router-link :to="{ name: 'discounts' }" class="btn btn-primary">
                    К списку программ лояльности
                </router-link>
            </div>
        </div>
        <div v-else>
            <p class="text-center">Элемент не найден.</p>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import API_ENDPOINTS from '@/services/api';
import DOMPurify from 'dompurify';
import BlockRenderer from '@/components/blocks/BlockRenderer.vue';
import Breadcrumbs from '@/components/Breadcrumbs.vue';

export default {
    name: 'DiscountDetail',
    components: { BlockRenderer, Breadcrumbs },

    data() {
        return {
            discount: null,
            loading: true,
        };
    },

    computed: {
        sortedBlocks() {
            if (!this.discount || !Array.isArray(this.discount.blocks)) {
                return [];
            }

            return [...this.discount.blocks].sort(
                (a, b) => (a.sort_order || 0) - (b.sort_order || 0),
            );
        },
        safeDescription() {
            return this.discount ? DOMPurify.sanitize(this.discount.description) : '';
        },
        breadcrumbs() {
            const crumbs = [
                { label: 'Программы лояльности', to: { name: 'discounts' } },
            ];
            if (this.discount) {
                crumbs.push({ label: this.discount.name, to: null });
            }
            return crumbs;
        },
        showPercentage() {
            if (!this.discount) {
                return false;
            }
            const p = this.discount.percentage;

            return p !== null && p !== undefined && p !== '';
        },
    },

    mounted() {
        axios.get(API_ENDPOINTS.discountDetails(this.$route.params.id))
            .then((response) => {
                this.discount = response.data.data;
            })
            .catch((error) => {
                console.error('Ошибка при загрузке скидки:', error);
            })
            .finally(() => {
                this.loading = false;

                this.$nextTick(() => {
                    const images = document.querySelectorAll('.discount-description img');
                    images.forEach((img) => {
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
};
</script>

<style>
.discount-description img {
    max-width: 100%;
    height: auto;
    width: auto;
    display: block;
    margin: 0 auto;
}
.discount-description {
    max-width: 100%;
    overflow: hidden;
}
</style>
