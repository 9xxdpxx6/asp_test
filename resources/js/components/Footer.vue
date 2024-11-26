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
                        <li><router-link :to="{ name: 'prices' }" class="text-light">Цены</router-link></li>
                        <li><router-link :to="{ name: 'about' }" class="text-light">О нас</router-link></li>
                        <li><router-link :to="{ name: 'blog' }" class="text-light">Новости</router-link></li>
                        <li><router-link :to="{ name: 'contacts' }" class="text-light">Контакты</router-link></li>
                    </ul>
                </div>

                <div class="col-md-4">
                    <router-link class="navbar-brand" :to="{ name: 'home' }">
                        <img :src="logo" alt="Logo" height="40" class="mb-2"/>
                    </router-link>
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
