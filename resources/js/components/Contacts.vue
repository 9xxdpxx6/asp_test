<template>
    <section class="contact-section">
        <!-- Map at the top -->
        <div class="container">
            <div class="map-container mb-4">
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3151.8354345091643!2d144.96305781561664!3d-37.8162189797517!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x6ad642af0f11fd81%3A0xf5776b1f3df54360!2sFlinders%20St%2C%20Melbourne%20VIC%203001%2C%20Australia!5e0!3m2!1sen!2sru!4v1632946886824!5m2!1sen!2sru"
                    width="100%"
                    height="450"
                    style="border:0;"
                    allowfullscreen
                    loading="lazy"
                ></iframe>
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
                        <li><strong>Адрес:</strong> 12345, Улица Пример, Город, Страна</li>
                        <li><strong>Рабочее время:</strong> Пн-Пт 9:00 - 18:00</li>
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

            <div class="branches mb-5">
                <h3 class="mb-4">Наши филиалы:</h3>
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Филиал 1</h5>
                                <p class="card-text">Адрес: 11111, Улица Филиал 1, Город, Страна</p>
                                <p class="card-text"><strong>Телефон:</strong> +1 (111) 111-11</p>
                                <p class="card-text"><strong>Email:</strong> branch1@example.com</p>
                                <div class="row mt-3">
                                    <div class="col-6">
                                        <button class="btn btn-primary w-100" @click="register('Филиал 1')">Записаться</button>
                                    </div>
                                    <div class="col-6">
                                        <button class="btn btn-outline-primary w-100" @click="showDetails('Филиал 1')">Подробнее</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Филиал 2</h5>
                                <p class="card-text">Адрес: 22222, Улица Филиал 2, Город, Страна</p>
                                <p class="card-text"><strong>Телефон:</strong> +2 (222) 222-22</p>
                                <p class="card-text"><strong>Email:</strong> branch2@example.com</p>
                                <div class="row mt-3">
                                    <div class="col-6">
                                        <button class="btn btn-primary w-100" @click="register('Филиал 2')">Записаться</button>
                                    </div>
                                    <div class="col-6">
                                        <button class="btn btn-outline-primary w-100" @click="showDetails('Филиал 2')">Подробнее</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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
