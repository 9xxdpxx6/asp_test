<template>
    <section class="cta-section section-spacing text-white">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-5 text-center text-lg-start">
                    <h2 class="display-6 fw-bold mb-3">Готовы начать обучение?</h2>
                    <p class="lead mb-4">
                        Оставьте заявку, и мы свяжемся с вами, чтобы ответить на все вопросы и помочь с записью.
                    </p>
                    <a href="tel:+79615262359" class="btn btn-outline-light btn-lg rounded-pill px-4">
                        <i class="fas fa-phone me-2"></i>+7 (961) 526-23-59
                    </a>
                </div>
                <div class="col-lg-7">
                    <div class="cta-form-card p-4 rounded-4">
                        <h5 class="fw-bold mb-3 text-dark">Обратный звонок</h5>
                        <template v-if="isSubmitted">
                            <div class="text-center py-4">
                                <i class="fas fa-check-circle text-success fa-3x mb-3"></i>
                                <h5 class="text-dark">Заявка отправлена!</h5>
                                <p class="text-muted">Мы свяжемся с вами в ближайшее время.</p>
                            </div>
                        </template>
                        <form v-else @submit.prevent="submitCallbackRequest">
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <input type="text" class="form-control form-control-lg" placeholder="Ваше имя *" v-model="form.full_name" required :disabled="isSubmitting">
                                </div>
                                <div class="col-sm-6">
                                    <input type="tel" class="form-control form-control-lg" placeholder="Телефон *" v-model="form.phone" required :disabled="isSubmitting">
                                </div>
                                <div class="col-12">
                                    <input type="email" class="form-control form-control-lg" placeholder="Электронная почта" v-model="form.email" :disabled="isSubmitting">
                                </div>
                                <div class="col-12">
                                    <textarea class="form-control" placeholder="Комментарий" v-model="form.comment" rows="2" :disabled="isSubmitting"></textarea>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary btn-lg w-100" :disabled="isSubmitting">
                                        <i class="fas fa-phone-volume me-2" v-if="!isSubmitting"></i>
                                        {{ isSubmitting ? 'Отправляем...' : 'Отправить заявку' }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
import axios from 'axios';
import API_ENDPOINTS from '@/services/api.js';

export default {
    name: 'CtaSection',
    data() {
        return {
            isSubmitted: false,
            isSubmitting: false,
            form: {
                full_name: '',
                phone: '',
                email: '',
                comment: '',
            },
        };
    },
    methods: {
        async submitCallbackRequest() {
            if (this.isSubmitting) return;
            this.isSubmitting = true;
            try {
                await axios.post(API_ENDPOINTS.callbackRequests, this.form);
                this.isSubmitted = true;
            } catch (error) {
                console.error('Ошибка при отправке запроса:', error);
            } finally {
                this.isSubmitting = false;
            }
        },
    },
};
</script>

<style scoped>
.cta-section {
    background: linear-gradient(135deg, var(--bs-primary), #2a52b8);
}

.cta-form-card {
    background: #fff;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
}
</style>
