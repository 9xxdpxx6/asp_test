<template>
    <section class="section-spacing">
        <div class="container">
            <div class="text-center mb-5">
                <h1 class="display-6 section-title">О нашей автошколе</h1>
                <p class="text-muted mt-3">Добро пожаловать в «Автошкола Политех»</p>
            </div>

            <div class="row align-items-center mb-5 g-4">
                <div class="col-md-7">
                    <p>
                        Автошкола «Политех» открыла свои двери для всех желающих обучиться вождению в 2012 году.
                        С тех пор мы успешно обучили тысячи учеников в 8 филиалах, удобно расположенных по всему Краснодару.
                    </p>
                    <p>
                        Наши современные автодромы с макетом реальных дорог обеспечивают идеальные условия для практических занятий,
                        где начинающие водители могут отточить свои навыки вождения под руководством опытных инструкторов.
                    </p>
                </div>
                <div class="col-md-5">
                    <img :src="images.drom" alt="Автодром" class="img-fluid rounded-3 img-ratio-3x2" />
                </div>
            </div>

            <div class="row align-items-center mb-5 g-4">
                <div class="col-md-5 order-md-1">
                    <img :src="images.comfort" alt="Комфортное обучение" class="img-fluid rounded-3 img-ratio-3x2" />
                </div>
                <div class="col-md-7 order-md-2">
                    <h3 class="fw-bold mb-3">Обучение с комфортом</h3>
                    <p>
                        Мы понимаем, что комфортное обучение — это залог успеха. Каждый ученик получает необходимые учебные материалы,
                        выбирает инструктора и составляет индивидуальный график занятий.
                    </p>
                    <p>
                        Вы можете выбрать автомобиль из нашего автопарка — как отечественные модели, так и иномарки.
                        Занятия проходят в небольших группах, что позволяет уделять внимание каждому ученику.
                    </p>
                </div>
            </div>

            <div class="row align-items-center mb-5 g-4">
                <div class="col-md-7">
                    <h3 class="fw-bold mb-3">Очная форма обучения</h3>
                    <p>
                        Все занятия проходят в очном формате с опытным преподавателем.
                        Вы можете задать любые вопросы, разобрать сложные ситуации на дороге.
                    </p>
                    <p>
                        Мы предоставляем доступ к теоретическим материалам, а также возможность
                        пройти пробный экзамен, аналогичный экзамену в ГИБДД.
                    </p>
                </div>
                <div class="col-md-5">
                    <img :src="images.online" alt="Обучение" class="img-fluid rounded-3 img-ratio-3x2" />
                </div>
            </div>

            <div class="row align-items-center mb-5 g-4">
                <div class="col-md-5 order-md-1">
                    <img :src="images.loyalty" alt="Программы лояльности" class="img-fluid rounded-3 img-ratio-3x2" />
                </div>
                <div class="col-md-7 order-md-2">
                    <h3 class="fw-bold mb-3">Программы лояльности</h3>
                    <p>
                        Мы предлагаем различные программы лояльности, которые помогут вам сэкономить.
                        Скидки студентам, оплата материнским капиталом и возможность рассрочки.
                    </p>
                </div>
            </div>

            <div class="text-center mt-4">
                <h3 class="fw-bold mb-3">Готовим грамотных водителей</h3>
                <p class="mx-auto" style="max-width: 700px;">
                    В условиях сложной дорожной обстановки мы стремимся выпускать уверенных водителей, готовых
                    к любым вызовам на дороге. Хотите стать таким водителем? Мы ждем вас!
                </p>
                <router-link :to="{ name: 'prices' }" class="btn btn-primary btn-lg rounded-pill px-4 mt-3">
                    <i class="fas fa-graduation-cap me-2"></i>Записаться на обучение
                </router-link>
            </div>
        </div>
    </section>
</template>

<script>
export default {
    name: 'About',
    data() {
        return {
            images: {
                comfort: '/images/about/comfort.jpg',
                drom: '/images/about/drom.jpg',
                loyalty: '/images/about/loyalty.jpg',
                online: '/images/about/online.jpg',
            },
        };
    },
};
</script>

<style scoped>
</style>
