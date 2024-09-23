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
                                <button class="btn btn-primary w-100 mb-2" @click="register(category.id)">
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

        <div class="mt-5">
            <h2 class="text-center mb-3">Что входит в цену за обучение?</h2>
            <div class="row">
                <div class="col-md-6">
                    <ul class="list-group mb-4">
                        <li class="list-group-item"><i class="fas fa-book-open"></i> Теоретические занятия</li>
                        <li class="list-group-item"><i class="fas fa-car"></i> Практические занятия на автодроме</li>
                        <li class="list-group-item"><i class="fas fa-user-graduate"></i> Индивидуальные занятия с инструктором</li>
                        <li class="list-group-item"><i class="fas fa-file-alt"></i> Учебные материалы и литература</li>
                        <li class="list-group-item"><i class="fas fa-clipboard-check"></i> Смоделированный экзамен</li>
                        <li class="list-group-item"><i class="fas fa-headset"></i> Поддержка на протяжении всего обучения</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <img src="https://via.placeholder.com/700x300" alt="Теоретические занятия" class="img-fluid rounded">
                </div>
            </div>
        </div>

        <div class="mt-5">
            <h2 class="text-center mb-3">Рассрочка на обучение</h2>
            <div class="row">
                <div class="col-md-6">
                    <p class="ms-3">
                        Сумма делится равными частями на удобное количество месяцев.
                    </p>
                    <ul class="list-group mb-4">
                        <li class="list-group-item"><i class="fas fa-calendar-alt"></i> Рассрочка на срок от 2 до 12 месяцев</li>
                        <li class="list-group-item"><i class="fas fa-money-bill-wave"></i> Без первоначального взноса</li>
                        <li class="list-group-item"><i class="fas fa-credit-card"></i> Удобные ежемесячные платежи</li>
                        <li class="list-group-item"><i class="fas fa-times-circle"></i> Возможность досрочного погашения без штрафов</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <img src="https://via.placeholder.com/700x300" alt="Рассрочка на обучение" class="img-fluid rounded">
                </div>
            </div>
            <h2 class="text-center mt-4">Запишитесь к нам, и мы поможем вам осуществить вашу мечту о водительских правах!</h2>
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
        };
    },
    methods: {
        register(categoryId) {
            alert(`Запись на категорию ${categoryId} выполнена!`);
        },
        showDetails(categoryId) {
            alert(`Показать детали для категории ${categoryId}`);
        },
    },
};
</script>

<style scoped>
.list-group-item {
    background-color: #f8f9fa; /* Цвет фона для элементов списка */
    border: none; /* Убираем границу */
}
</style>
