<template>
    <footer class="bg-info text-light py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5>Контакты</h5>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-phone"></i> +7 (123) 456-7890</li>
                        <li><i class="fas fa-envelope"></i> info@politech-auto.ru</li>
                        <li><i class="fas fa-map-marker-alt"></i> ул. Примерная, 10, Краснодар</li>
                    </ul>
                </div>

                <div class="col-md-4">
                    <h5>Полезные ссылки</h5>
                    <ul class="list-unstyled">
                        <li><router-link to="/prices" class="text-light">Цены</router-link></li>
                        <li><router-link to="/about" class="text-light">О нас</router-link></li>
                        <li><router-link to="/blog" class="text-light">Блог</router-link></li>
                        <li><router-link to="/contacts" class="text-light">Контакты</router-link></li>
                    </ul>
                </div>

                <div class="col-md-4">
                    <img :src="logo" alt="Logo" height="40" class="mb-2"/>
                    <p>Качественное обучение вождению для начинающих и профессионалов. Мы готовим водителей с гарантией успеха на дорогах.</p>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-12 text-center lead">
                    <a href="https://t.me/yourtelegram" class="text-light me-3"><i class="fab fa-telegram"></i></a>
                    <a href="https://vk.com/yourvk" class="text-light me-3"><i class="fab fa-vk"></i></a>
                </div>
            </div>
        </div>

        <div class="text-center py-3 mt-4">
            <p class="mb-0">&copy; 2024 Автошкола Политех. Все права защищены.</p>
        </div>
    </footer>
</template>

<script>
export default {
    name: 'Footer',
    data() {
        return {
            logo: '/logo.png'
        }
    },
};
</script>

<style scoped>
footer {
    font-size: 14px;
}

footer a {
    text-decoration: none;
}

footer a:hover {
    text-decoration: underline;
}

.fas, .fab {
    margin-right: 8px;
}

.bg-secondary {
    background-color: #343a40 !important;
}
</style>
