<template>
    <section id="callback" class="cta-section section-spacing text-white">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-5 text-center text-lg-start">
                    <h2 class="display-6 fw-bold mb-3">{{ content.heading }}</h2>
                    <p class="lead mb-4">{{ content.subheading }}</p>
                    <a :href="phoneHref" class="btn btn-outline-light btn-lg rounded-pill px-4">
                        <i class="fas fa-phone me-2"></i>{{ content.phone_label }}
                    </a>
                </div>
                <div class="col-lg-7">
                    <div class="cta-form-card p-4 rounded-4">
                        <h5 class="fw-bold mb-3 text-dark">{{ content.form_title }}</h5>
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
                                    <input type="text" class="form-control form-control-lg" :placeholder="content.name_placeholder" v-model="form.full_name" required :disabled="isSubmitting">
                                </div>
                                <div class="col-sm-6">
                                    <input type="tel" class="form-control form-control-lg" :placeholder="content.phone_placeholder" v-model="form.phone" required :disabled="isSubmitting">
                                </div>
                                <div class="col-12">
                                    <input type="email" class="form-control form-control-lg" :placeholder="content.email_placeholder" v-model="form.email" :disabled="isSubmitting">
                                </div>
                                <div class="col-12">
                                    <textarea class="form-control" :placeholder="content.comment_placeholder" v-model="form.comment" rows="2" :disabled="isSubmitting"></textarea>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary btn-lg w-100" :disabled="isSubmitting">
                                        <i class="fas fa-phone-volume me-2" v-if="!isSubmitting"></i>
                                        {{ isSubmitting ? 'Отправляем...' : content.button_text }}
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

const fallbackContent = {
    heading: 'Готовы начать обучение?',
    subheading: 'Оставьте заявку, и мы свяжемся с вами, чтобы ответить на все вопросы и помочь с записью.',
    phone_label: '+7 (961) 526-23-59',
    form_title: 'Обратный звонок',
    name_placeholder: 'Ваше имя *',
    phone_placeholder: 'Телефон *',
    email_placeholder: 'Электронная почта',
    comment_placeholder: 'Комментарий',
    button_text: 'Отправить заявку',
};

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
            content: { ...fallbackContent },
        };
    },
    computed: {
        phoneHref() {
            const value = String(this.content.phone_label || '').trim();

            if (!value) {
                return 'tel:+79615262359';
            }

            if (value.startsWith('+')) {
                return `tel:${value.replace(/[^\d+]/g, '')}`;
            }

            const digits = value.replace(/\D/g, '');

            if (!digits) {
                return 'tel:+79615262359';
            }

            if (digits.length === 11 && digits.startsWith('8')) {
                return `tel:+7${digits.slice(1)}`;
            }

            return `tel:+${digits}`;
        },
    },
    mounted() {
        axios
            .get(API_ENDPOINTS.callbackSection)
            .then((response) => {
                const payload = response.data?.data;
                if (!payload) {
                    return;
                }

                this.content = {
                    heading: payload.heading || fallbackContent.heading,
                    subheading: payload.subheading || fallbackContent.subheading,
                    phone_label: payload.phone_label || fallbackContent.phone_label,
                    form_title: payload.form_title || fallbackContent.form_title,
                    name_placeholder: payload.name_placeholder || fallbackContent.name_placeholder,
                    phone_placeholder: payload.phone_placeholder || fallbackContent.phone_placeholder,
                    email_placeholder: payload.email_placeholder || fallbackContent.email_placeholder,
                    comment_placeholder: payload.comment_placeholder || fallbackContent.comment_placeholder,
                    button_text: payload.button_text || fallbackContent.button_text,
                };
            })
            .catch(() => {});
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
