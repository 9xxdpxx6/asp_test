<template>
    <transition name="fade">
        <div class="modal-overlay" @click.self="closeModal">
            <div class="modal-dialog-box">
                <template v-if="isSubmitted">
                    <div class="text-center py-4">
                        <i class="fas fa-check-circle text-success fa-3x mb-3"></i>
                        <h5>Заявка успешно отправлена!</h5>
                        <p class="text-muted">Спасибо за запись. Мы свяжемся с вами в ближайшее время.</p>
                    </div>
                </template>
                <template v-else>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">{{ modalTitle }}</h5>
                        <button type="button" class="btn-close" @click="closeModal"></button>
                    </div>
                    <form @submit.prevent="submitCallbackRequest">
                        <div class="mb-3">
                            <label for="cb_full_name" class="form-label">ФИО</label>
                            <input type="text" class="form-control" id="cb_full_name" v-model="form.full_name" required :disabled="isSubmitting">
                        </div>
                        <div class="mb-3">
                            <label for="cb_phone" class="form-label">Телефон</label>
                            <input type="tel" class="form-control" id="cb_phone" v-model="form.phone" required :disabled="isSubmitting">
                        </div>
                        <div class="mb-3">
                            <label for="cb_email" class="form-label">Почта</label>
                            <input type="email" class="form-control" id="cb_email" v-model="form.email" :disabled="isSubmitting">
                        </div>
                        <div class="mb-3">
                            <label for="cb_comment" class="form-label">Комментарий</label>
                            <textarea class="form-control" id="cb_comment" v-model="form.comment" rows="3" :disabled="isSubmitting"></textarea>
                        </div>
                        <div class="d-flex justify-content-end gap-2">
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
</template>

<script>
import axios from "axios";
import API_ENDPOINTS from '@/services/api.js';

export default {
    name: 'CallbackForm',
    props: {
        category: {
            type: Object,
            default: null,
        },
        comment: {
            type: String,
            default: "",
        },
    },
    data() {
        return {
            isSubmitted: false,
            isSubmitting: false,
            form: {
                full_name: "",
                phone: "",
                email: "",
                comment: "",
            },
        };
    },
    computed: {
        modalTitle() {
            if (this.category) {
                return `Запись на обучение — ${this.category.name}`;
            }
            return 'Запись на обратный звонок';
        },
    },
    mounted() {
        if (this.category) {
            this.form.comment = `Запись на категорию: ${this.category.name}`;
        } else if (this.comment) {
            this.form.comment = this.comment;
        }
    },
    methods: {
        closeModal() {
            this.$emit("close");
            this.isSubmitted = false;
            this.isSubmitting = false;
            this.resetForm();
        },
        resetForm() {
            this.form = {
                full_name: "",
                phone: "",
                email: "",
                comment: this.category ? `Запись на категорию: ${this.category.name}` : (this.comment || ""),
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
                console.error("Ошибка при отправке запроса:", error);
            } finally {
                this.isSubmitting = false;
            }
        },
    },
};
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
    z-index: 1050;
}

.modal-dialog-box {
    background: white;
    color: var(--bs-body-color);
    padding: 1.5rem;
    border-radius: 0.75rem;
    width: 100%;
    max-width: 440px;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
    margin: 1rem;
}

.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
