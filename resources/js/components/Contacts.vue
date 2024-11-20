<template>
    <section class="contact-section">
        <!-- Map at the top -->
        <div class="container">
            <div class="map-container mb-4">
                <div style="position:relative;overflow:hidden;"><a href="https://yandex.ru/maps/35/krasnodar/?utm_medium=mapframe&utm_source=maps" style="color:#eee;font-size:12px;position:absolute;top:0px;">Краснодар</a><a href="https://yandex.ru/maps/35/krasnodar/?ll=39.003489%2C45.043432&mode=search&sctx=ZAAAAAgBEAAaKAoSCYwubw7XfENAEZv%2B7EeKhEZAEhIJfEYiNIKNzz8RtJQsJ6H0uT8iBgABAgMEBSgKOABAI0gBagJydZ0BzczMPaABAKgBAL0BV1ve18IBBtGC5tGcBYICIdCw0LLRgtC%2B0YjQutC%2B0LvQsCDQv9C%2B0LvQuNGC0LXRhYoCAJICAJoCDGRlc2t0b3AtbWFwcw%3D%3D&sll=39.003489%2C45.043432&sspn=0.015407%2C0.006336&text=%D0%B0%D0%B2%D1%82%D0%BE%D1%88%D0%BA%D0%BE%D0%BB%D0%B0%20%D0%BF%D0%BE%D0%BB%D0%B8%D1%82%D0%B5%D1%85&utm_medium=mapframe&utm_source=maps&whatshere%5Bpoint%5D=39.003489%2C45.043432&whatshere%5Bzoom%5D=16&z=17" style="color:#eee;font-size:12px;position:absolute;top:14px;">Спортивная улица, 2кЛ — Яндекс Карты</a><iframe src="https://yandex.ru/map-widget/v1/?ll=39.003489%2C45.043432&mode=search&sctx=ZAAAAAgBEAAaKAoSCYwubw7XfENAEZv%2B7EeKhEZAEhIJfEYiNIKNzz8RtJQsJ6H0uT8iBgABAgMEBSgKOABAI0gBagJydZ0BzczMPaABAKgBAL0BV1ve18IBBtGC5tGcBYICIdCw0LLRgtC%2B0YjQutC%2B0LvQsCDQv9C%2B0LvQuNGC0LXRhYoCAJICAJoCDGRlc2t0b3AtbWFwcw%3D%3D&sll=39.003489%2C45.043432&sspn=0.015407%2C0.006336&text=%D0%B0%D0%B2%D1%82%D0%BE%D1%88%D0%BA%D0%BE%D0%BB%D0%B0%20%D0%BF%D0%BE%D0%BB%D0%B8%D1%82%D0%B5%D1%85&whatshere%5Bpoint%5D=39.003489%2C45.043432&whatshere%5Bzoom%5D=16&z=17" height="400" frameborder="1" allowfullscreen="true" style="position:relative; width: 100%"></iframe></div>
            </div>
        </div>

        <!-- Contact information in two columns -->
        <div class="container">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h3>Свяжитесь с нами</h3>
                    <p>Мы всегда готовы ответить на ваши вопросы и помочь вам!</p>
                    <ul class="list-unstyled">
                        <li><strong>Телефон:</strong> <a href="tel:+123456789">+1 (234) 567-89</a></li>
                        <li><strong>Электронная почта:</strong> <a href="mailto:info@example.com">info@example.com</a></li>
                        <li><strong>Адрес:</strong> Спортивная ул., 2, корп. Л, микрорайон Табачная Фабрика, Краснодар, Россия</li>
                        <li>
                            <div>
                                <strong>Рабочее время:</strong> Пн-Пт 9:00 - 17:00
                            </div>
                            <div>
                                Сб-Вс - Выхолдной
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h3>Дополнительные контакты</h3>
                    <ul class="list-unstyled">
                        <li><strong>Отдел продаж:</strong> <a href="tel:+123456790">+1 (234) 567-90</a></li>
                        <li><strong>Техническая поддержка:</strong> <a href="tel:+123456791">+1 (234) 567-91</a></li>
                        <li><strong>HR отдел:</strong> <a href="mailto:hr@example.com">hr@example.com</a></li>
                        <li><strong>Факс:</strong> +1 (234) 567-92</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
export default {
    name: 'Contacts',
}
</script>

<style scoped>
.map-container {
    width: 100%;
    height: 450px;
}
</style>
