<template>
    <section class="section-spacing">
        <div class="container">
            <div class="text-center mb-5">
                <h1 class="display-6 section-title">Наши адреса</h1>
                <p class="text-muted mt-3">ул. Спортивная, 2кЛ | ул. Старокубанская, 88/5</p>
            </div>

            <!-- Map at the top -->
            <div class="map-container mb-4 rounded-3 overflow-hidden">
                <div style="position:relative;overflow:hidden;">
                    <a href="https://yandex.ru/maps/35/krasnodar/?utm_medium=mapframe&utm_source=maps" style="color:#eee;font-size:12px;position:absolute;top:0px;">Краснодар</a>
                    <a href="https://yandex.ru/maps/35/krasnodar/?ll=39.003489%2C45.043432&mode=search&sctx=ZAAAAAgBEAAaKAoSCYwubw7XfENAEZv%2B7EeKhEZAEhIJfEYiNIKNzz8RtJQsJ6H0uT8iBgABAgMEBSgKOABAI0gBagJydZ0BzczMPaABAKgBAL0BV1ve18IBBtGC5tGcBYICIdCw0LLRgtC%2B0YjQutC%2B0LvQsCDQv9C%2B0LvQuNGC0LXRhYoCAJICAJoCDGRlc2t0b3AtbWFwcw%3D%3D&sll=39.003489%2C45.043432&sspn=0.015407%2C0.006336&text=%D0%B0%D0%B2%D1%82%D0%BE%D1%88%D0%BA%D0%BE%D0%BB%D0%B0%20%D0%BF%D0%BE%D0%BB%D0%B8%D1%82%D0%B5%D1%85&utm_medium=mapframe&utm_source=maps&whatshere%5Bpoint%5D=39.003489%2C45.043432&whatshere%5Bzoom%5D=16&z=17" style="color:#eee;font-size:12px;position:absolute;top:14px;">Спортивная улица, 2кЛ — Яндекс Карты</a>
                    <iframe src="https://yandex.ru/map-widget/v1/?ll=39.003489%2C45.043432&mode=search&sctx=ZAAAAAgBEAAaKAoSCYwubw7XfENAEZv%2B7EeKhEZAEhIJfEYiNIKNzz8RtJQsJ6H0uT8iBgABAgMEBSgKOABAI0gBagJydZ0BzczMPaABAKgBAL0BV1ve18IBBtGC5tGcBYICIdCw0LLRgtC%2B0YjQutC%2B0LvQsCDQv9C%2B0LvQuNGC0LXRhYoCAJICAJoCDGRlc2t0b3AtbWFwcw%3D%3D&sll=39.003489%2C45.043432&sspn=0.015407%2C0.006336&text=%D0%B0%D0%B2%D1%82%D0%BE%D1%88%D0%BA%D0%BE%D0%BB%D0%B0%20%D0%BF%D0%BE%D0%BB%D0%B8%D1%82%D0%B5%D1%85&whatshere%5Bpoint%5D=39.003489%2C45.043432&whatshere%5Bzoom%5D=16&z=17" height="400" frameborder="1" allowfullscreen="true" style="position:relative; width: 100%"></iframe>
                </div>
            </div>

            <!-- Основной адрес -->
            <h3 class="mb-3">ул. Спортивная, 2кЛ</h3>
            <div class="image-container mb-4 text-center">
                <img :src="images.enter" alt="Вход в автошколу (Спортивная)" class="img-fluid rounded-3">
            </div>

            <div class="mb-5 lead">
                <ul class="list-unstyled">
                    <li>
                        <strong>Адрес:</strong>
                        г. Краснодар, р-н Табачной фабрики, ул. Спортивная, д. 2, к. Л.
                    </li>
                    <li>
                        <strong>Режим работы:</strong> Пн–Пт 09:00–17:00, Сб–Вс — выходной
                    </li>
                </ul>
            </div>

            <!-- Дополнительный адрес -->
            <h3 class="mb-3">ул. Старокубанская, 88/5</h3>
            <div class="row mb-4">
                <div class="col-md-6 mb-3">
                    <img :src="images.starokubEnter" alt="Вход (Старокубанская)" class="img-fluid starokub-img rounded-3">
                </div>
                <div class="col-md-6 mb-3">
                    <img :src="images.starokubBanner" alt="Баннер (Старокубанская)" class="img-fluid starokub-img rounded-3">
                </div>
            </div>

            <div class="mb-5 lead">
                <ul class="list-unstyled">
                    <li>
                        <strong>Адрес:</strong>
                        г. Краснодар, ул. Старокубанская, 88/5
                    </li>
                    <li>
                        <strong>Режим работы:</strong> Вт, Чт 09:00–12:00
                    </li>
                </ul>
            </div>

            <!-- Контакты -->
            <div class="mb-4 lead">
                <h3>Свяжитесь с нами</h3>
                <p>Мы всегда готовы ответить на ваши вопросы и помочь вам!</p>
                <ul class="list-unstyled">
                    <li><strong>Телефон:</strong> <a href="tel:+79615262359">+7-961-526-23-59</a></li>
                    <li><strong>Электронная почта:</strong> <a href="mailto:avtoshkola-politekh@mail.ru">avtoshkola-politekh@mail.ru</a></li>
                </ul>
            </div>
        </div>
    </section>
</template>

<script>
export default {
    name: 'Contacts',

    data() {
        return {
            images: {
                enter: '/images/contacts/enter.JPG',
                starokubEnter: '/images/contacts/starokub-enter.jpg',
                starokubBanner: '/images/contacts/starokub-banner.jpg',
            }
        }
    },
}
</script>

<style scoped>
.map-container {
    width: 100%;
    height: 450px;
}

.image-container img {
    width: 100%;
    /*max-width: 600px;*/
    height: auto;
}

.starokub-img {
    width: 100%;
    max-height: 500px;
    object-fit: cover;
    object-position: center top;
    border-radius: 0.5rem;
}
</style>
