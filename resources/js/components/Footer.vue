<template>
    <footer class="bg-primary text-light py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5>Контакты</h5>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-phone me-3"></i>+7-961-526-23-59</li>
                        <li><i class="fas fa-envelope me-3"></i>avtoshkola-politekh@mail.ru</li>
                        <li><i class="fas fa-map-marker-alt me-3"></i> г. Краснодар, р-н Табачной фабрики, ул. Спортивная, д. 2, к. Л. </li>
                    </ul>
                </div>

                <div class="col-md-4">
                    <h5>Полезные ссылки</h5>
                    <ul class="list-unstyled">
                        <li><router-link :to="{ name: 'prices' }" class="text-light">Цены</router-link></li>
                        <li><router-link :to="{ name: 'blog' }" class="text-light">Новости</router-link></li>
                        <li><router-link :to="{ name: 'contacts' }" class="text-light">Контакты</router-link></li>
                        <li><a :href="pdf" class="text-light" download target="_blank">Акт самообследования автошколы за 2024 год</a></li>
                        <li><a :href="pdfDownloadUrl" download="act_selfcheck_2024.pdf">Скачать PDF</a></li>
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
                    <a href="https://t.me/kubstu_official" target="_blank" class="text-light me-3"><i class="fab fa-telegram"></i></a>
                    <a href="https://vk.com/kubstu_official" target="_blank" class="text-light me-3"><i class="fab fa-vk"></i></a>
                </div>
            </div>
        </div>

        <div class="text-center py-3 mt-4">
            <p class="mb-0">&copy; {{ currentYear }} Автошкола Политех. Все права защищены.</p>
        </div>
    </footer>
</template>

<script>
export default {
    name: 'Footer',
    data() {
        return {
            logo: '/logo.png',
            pdfDownloadUrl: '/act_selfcheck',
            currentYear: new Date().getFullYear(),
        };
    },
};
</script>

<style scoped>
footer {
    font-size: 0.875rem;
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
