<template>
    <section class="section-spacing">
        <div class="container">
            <div class="text-center mb-5">
                <h1 class="display-6 section-title">{{ page.hero_title }}</h1>
                <p v-if="page.hero_subtitle" class="text-muted mt-3">{{ page.hero_subtitle }}</p>
            </div>

            <div
                v-for="(block, index) in blocks"
                :key="block.id || index"
                class="row align-items-center mb-5 g-4"
            >
                <template v-if="block.image_on_left">
                    <div class="col-md-5 order-md-1">
                        <img
                            :src="block.image"
                            :alt="blockTitleAlt(block)"
                            class="img-fluid rounded-3 img-ratio-3x2"
                            loading="lazy"
                        />
                    </div>
                    <div class="col-md-7 order-md-2">
                        <h3 v-if="block.title" class="fw-bold mb-3">{{ block.title }}</h3>
                        <p v-for="(para, pi) in splitParagraphs(block.description)" :key="pi" class="mb-3">
                            {{ para }}
                        </p>
                    </div>
                </template>
                <template v-else>
                    <div class="col-md-7">
                        <h3 v-if="block.title" class="fw-bold mb-3">{{ block.title }}</h3>
                        <p v-for="(para, pi) in splitParagraphs(block.description)" :key="pi" class="mb-3">
                            {{ para }}
                        </p>
                    </div>
                    <div class="col-md-5">
                        <img
                            :src="block.image"
                            :alt="blockTitleAlt(block)"
                            class="img-fluid rounded-3 img-ratio-3x2"
                            loading="lazy"
                        />
                    </div>
                </template>
            </div>

            <div class="text-center mt-4">
                <h3 class="fw-bold mb-3">{{ page.cta_title }}</h3>
                <p class="mx-auto" style="max-width: 700px;">
                    {{ page.cta_text }}
                </p>
                <div class="mt-3 d-inline-block">
                    <a
                        v-if="ctaIsExternal"
                        :href="page.cta_href"
                        class="btn btn-primary btn-lg rounded-pill px-4 text-decoration-none"
                        target="_blank"
                        rel="noopener noreferrer"
                    >
                        <i v-if="ctaIconClass" :class="[ctaIconClass, 'me-2']"></i>{{ page.cta_button_text }}
                    </a>
                    <a
                        v-else-if="ctaIsAnchor"
                        :href="page.cta_href"
                        class="btn btn-primary btn-lg rounded-pill px-4 text-decoration-none"
                    >
                        <i v-if="ctaIconClass" :class="[ctaIconClass, 'me-2']"></i>{{ page.cta_button_text }}
                    </a>
                    <router-link
                        v-else
                        :to="page.cta_href || '/prices'"
                        class="btn btn-primary btn-lg rounded-pill px-4 text-decoration-none"
                    >
                        <i v-if="ctaIconClass" :class="[ctaIconClass, 'me-2']"></i>{{ page.cta_button_text }}
                    </router-link>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
import axios from 'axios';
import API_ENDPOINTS from '@/services/api';

const fallbackPage = {
    hero_title: 'О нашей автошколе',
    hero_subtitle: 'Добро пожаловать в «Автошкола Политех»',
    cta_title: 'Готовим грамотных водителей',
    cta_text:
        'В условиях сложной дорожной обстановки мы стремимся выпускать уверенных водителей, готовых к любым вызовам на дороге. Хотите стать таким водителем? Мы ждем вас!',
    cta_button_text: 'Записаться на обучение',
    cta_icon: 'fas fa-graduation-cap',
    cta_href: '/prices',
};

const fallbackBlocks = [
    {
        id: 1,
        title: null,
        description:
            'Автошкола «Политех» открыла свои двери для всех желающих обучиться вождению в 2012 году.\nС тех пор мы успешно обучили тысячи учеников в 8 филиалах, удобно расположенных по всему Краснодару.\n\nНаши современные автодромы с макетом реальных дорог обеспечивают идеальные условия для практических занятий, где начинающие водители могут отточить свои навыки вождения под руководством опытных инструкторов.',
        image: '/images/about/drom.jpg',
        image_on_left: false,
    },
    {
        id: 2,
        title: 'Обучение с комфортом',
        description:
            'Мы понимаем, что комфортное обучение — это залог успеха. Каждый ученик получает необходимые учебные материалы, выбирает инструктора и составляет индивидуальный график занятий.\n\nВы можете выбрать автомобиль из нашего автопарка — как отечественные модели, так и иномарки. Занятия проходят в небольших группах, что позволяет уделять внимание каждому ученику.',
        image: '/images/about/comfort.jpg',
        image_on_left: true,
    },
    {
        id: 3,
        title: 'Очная форма обучения',
        description:
            'Все занятия проходят в очном формате с опытным преподавателем. Вы можете задать любые вопросы, разобрать сложные ситуации на дороге.\n\nМы предоставляем доступ к теоретическим материалам, а также возможность пройти пробный экзамен, аналогичный экзамену в ГИБДД.',
        image: '/images/about/online.jpg',
        image_on_left: false,
    },
    {
        id: 4,
        title: 'Программы лояльности',
        description:
            'Мы предлагаем различные программы лояльности, которые помогут вам сэкономить. Скидки студентам, оплата материнским капиталом и возможность рассрочки.',
        image: '/images/about/loyalty.jpg',
        image_on_left: true,
    },
];

function splitParagraphs(text) {
    if (!text) {
        return [];
    }

    return text
        .split(/\n+/)
        .map((s) => s.trim())
        .filter(Boolean);
}

export default {
    name: 'About',
    data() {
        return {
            page: { ...fallbackPage },
            blocks: [...fallbackBlocks],
        };
    },
    computed: {
        ctaIconClass() {
            return (this.page.cta_icon || 'fas fa-graduation-cap').trim();
        },
        ctaIsExternal() {
            return /^https?:\/\//i.test(this.page.cta_href || '');
        },
        ctaIsAnchor() {
            return (this.page.cta_href || '').startsWith('#');
        },
    },
    methods: {
        blockTitleAlt(block) {
            return block.title || 'О школе';
        },
        splitParagraphs,
        applyPayload(payload) {
            if (payload.settings) {
                this.page = {
                    ...fallbackPage,
                    ...payload.settings,
                };
            }

            if (Array.isArray(payload.blocks) && payload.blocks.length) {
                this.blocks = payload.blocks.map((b) => ({
                    ...b,
                    image_on_left: typeof b.image_on_left === 'boolean' ? b.image_on_left : !!b.image_on_left,
                }));
            }
        },
    },
    mounted() {
        axios
            .get(API_ENDPOINTS.aboutPage)
            .then((response) => {
                const data = response.data || {};

                if (data.settings) {
                    this.applyPayload(data);
                }
            })
            .catch((error) => {
                console.error('Ошибка при загрузке страницы «О нас»:', error);
            });
    },
};
</script>

<style scoped>
</style>
