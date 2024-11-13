<template>
    <div class="container">
        <h1 class="text-center mb-3">Цены на обучение</h1>

        <div class="row">
            <div class="col-md-4 mb-4" v-for="category in priceCategories" :key="category.id">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h2 class="card-title">{{ category.title }}</h2>
                        <p class="card-text">{{ category.description }}</p>
                        <p><strong>Цена:</strong> {{ category.price }} руб.</p>
                        <p><strong>Срок обучения:</strong> {{ category.duration }}</p>
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
                    <h5>Запись на {{ selectedCategory?.title }}</h5>
                    <form @submit.prevent="submitForm">
                        <div class="mb-3">
                            <label for="fullName" class="form-label">ФИО</label>
                            <input type="text" class="form-control" id="fullName" v-model="form.fullName" required>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Телефон</label>
                            <input type="tel" class="form-control" id="phone" v-model="form.phone" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Почта</label>
                            <input type="email" class="form-control" id="email" v-model="form.email" required>
                        </div>
                        <div class="mb-3">
                            <label for="comment" class="form-label">Комментарий (опционально)</label>
                            <textarea class="form-control" id="comment" v-model="form.comment"></textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" @click="closeModal">Отмена</button>
                            <button type="submit" class="btn btn-primary">Отправить</button>
                        </div>
                    </form>
                </div>
            </div>
        </transition>
    </div>
</template>

<script>
export default {
    data() {
        return {
            priceCategories: [
                { id: 1, title: 'Категория A', description: 'Мотоциклы', price: 15000, duration: '1.5 месяца' },
                { id: 2, title: 'Категория B', description: 'Легковые автомобили', price: 25000, duration: '2 месяца' },
                { id: 3, title: 'Категория C', description: 'Грузовые автомобили', price: 30000, duration: '2.5 месяца' },
                { id: 4, title: 'Категория D', description: 'Автобусы', price: 40000, duration: '3 месяца' },
                { id: 5, title: 'Категория E', description: 'Автопоезда (с прицепом)', price: 45000, duration: '3 месяца' },
            ],
            selectedCategory: null,
            isModalOpen: false,
            form: {
                fullName: '',
                phone: '',
                email: '',
                comment: ''
            }
        };
    },
    methods: {
        openModal(category) {
            this.selectedCategory = category;
            this.isModalOpen = true;
        },
        closeModal() {
            this.isModalOpen = false;
        },
        submitForm() {
            alert(`Вы записались на категорию ${this.selectedCategory.title}!`);
            this.isModalOpen = false;
            // Сбрасываем форму
            this.form.fullName = '';
            this.form.phone = '';
            this.form.email = '';
            this.form.comment = '';
        }
    }
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

/* Плавный переход для модального окна */
.fade-enter-active, .fade-leave-active {
    transition: opacity 0.15s ease, transform 0.15s ease;
}
.fade-enter, .fade-leave-to /* .fade-leave-active в <2.1.8 */ {
    opacity: 0;
    transform: translateY(-10px);
}
</style>
