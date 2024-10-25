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
                                <button class="btn btn-primary w-100 mb-2" @click="openModal(category)">
                                    Записаться
                                </button>
                            </div>
                            <div class="col-6">
                                <button class="btn btn-outline-primary w-100" @click="showDetails(category.id)">
                                    Подробнее
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Модальное окно для записи -->
        <div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="registerModalLabel">Запись на {{ selectedCategory?.title }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
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
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                                <button type="submit" class="btn btn-primary">Отправить</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
export default {
    name: 'Prices',
    data() {
        return {
            priceCategories: [
                { id: 1, title: 'Категория A', description: 'Мотоциклы', price: 15000, duration: '1.5 месяца' },
                { id: 2, title: 'Категория B', description: 'Легковые автомобили', price: 25000, duration: '2 месяца' },
                { id: 3, title: 'Категория C', description: 'Грузовые автомобили', price: 30000, duration: '2.5 месяца' },
                { id: 4, title: 'Категория D', description: 'Автобусы', price: 40000, duration: '3 месяца' },
                { id: 5, title: 'Категория E', description: 'Автопоезда (с прицепом)', price: 45000, duration: '3 месяца' },
            ],
            selectedCategory: null, // Выбранная категория
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
            const modal = new bootstrap.Modal(document.getElementById('registerModal'));
            modal.show();
        },
        submitForm() {
            console.log('ФИО:', this.form.fullName);
            console.log('Телефон:', this.form.phone);
            console.log('Почта:', this.form.email);
            console.log('Комментарий:', this.form.comment);

            // Здесь можно отправить данные на сервер
            alert(`Вы записались на категорию ${this.selectedCategory.title}!`);

            // Сбрасываем форму
            this.form.fullName = '';
            this.form.phone = '';
            this.form.email = '';
            this.form.comment = '';

            // Закрыть модальное окно
            const modal = bootstrap.Modal.getInstance(document.getElementById('registerModal'));
            modal.hide();
        },
        showDetails(categoryId) {
            alert(`Показать детали для категории ${categoryId}`);
        }
    }
};
</script>

<style scoped>
/* Стили для модального окна */
.modal-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.modal-title {
    font-size: 1.5rem;
}

.modal-footer {
    background-color: #f8f9fa;
    border-top: 1px solid #dee2e6;
}

button {
    margin-right: 10px;
}
</style>
