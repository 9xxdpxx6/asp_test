<template>
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
                    <h5>Запишитесь на обратный звонок</h5>
                    <form @submit.prevent="submitCallbackRequest">
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
                            <textarea class="form-control" id="comment" v-model="form.comment">{{ comment }}</textarea>
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
</template>

<script>
import axios from "axios";
import API_ENDPOINTS from "@/services/api.js";

export default {
    props: {
        isModalOpen: {
            type: Boolean,
            required: true,
        },
        comment: {
            type: String,
            default: "",
        },
    },
    data() {
        return {
            isSubmitted: false,
            form: {
                full_name: "",
                phone: "",
                email: "",
                comment: "", // Будет установлено в mounted
            },
        };
    },
    mounted() {
        // Инициализируем значение comment при монтировании
        this.form.comment = this.comment;
    },
    watch: {
        // Если comment в родителе меняется, обновляем форму
        comment(newComment) {
            this.form.comment = newComment;
        },
    },
    methods: {
        closeModal() {
            this.$emit("close"); // Уведомляем родительский компонент о закрытии
            this.isSubmitted = false;
            this.resetForm();
        },
        resetForm() {
            this.form = {
                full_name: "",
                phone: "",
                email: "",
                comment: this.comment, // Сбрасываем к начальному значению
            };
        },
        submitCallbackRequest() {
            axios
                .post(API_ENDPOINTS.callbackRequests, this.form)
                .then(() => {
                    this.isSubmitted = true;
                    setTimeout(() => {
                        this.closeModal();
                    }, 2000);
                })
                .catch((error) => {
                    console.error("Ошибка при отправке запроса:", error);
                });
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
</style>
