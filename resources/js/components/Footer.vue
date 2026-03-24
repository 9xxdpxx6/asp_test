<template>
    <footer class="bg-primary text-light py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5>Контакты</h5>
                    <ul class="list-unstyled">
                        <li>
                            <i class="fas fa-phone me-3"></i>
                            <a :href="telHref" class="text-light">{{ display.phone }}</a>
                        </li>
                        <li>
                            <i class="fas fa-envelope me-3"></i>
                            <a :href="mailtoHref" class="text-light">{{ display.email }}</a>
                        </li>
                        <li v-if="display.address">
                            <i class="fas fa-map-marker-alt me-3"></i>
                            <span>{{ display.address }}</span>
                        </li>
                    </ul>
                </div>

                <div class="col-md-4">
                    <h5>Полезные ссылки</h5>
                    <ul class="list-unstyled">
                        <li><router-link :to="{ name: 'prices' }" class="text-light">Цены</router-link></li>
                        <li><router-link :to="{ name: 'blog' }" class="text-light">Новости</router-link></li>
                        <li><router-link :to="{ name: 'contacts' }" class="text-light">Контакты</router-link></li>
                        <li v-for="doc in display.documents" :key="doc.id">
                            <a
                                :href="doc.download_url"
                                class="text-light"
                                rel="noopener noreferrer"
                                download
                            >{{ doc.title }}</a>
                        </li>
                    </ul>
                </div>

                <div class="col-md-4">
                    <router-link class="navbar-brand" :to="{ name: 'home' }">
                        <img :src="logo" alt="Logo" height="40" class="mb-2"/>
                    </router-link>
                    <p v-if="display.logo_description" class="mb-0">{{ display.logo_description }}</p>
                </div>
            </div>

            <div v-if="display.social.length" class="row mt-3">
                <div class="col-md-12 text-center lead footer-social-row">
                    <a
                        v-for="(item, idx) in display.social"
                        :key="item.code + '-' + idx"
                        :href="item.url"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="text-light footer-social-link"
                        :aria-label="item.label"
                    >
                        <img
                            :src="item.icon_url"
                            :alt="item.label"
                            class="footer-social-img"
                            width="36"
                            height="36"
                            loading="lazy"
                        />
                    </a>
                </div>
            </div>
        </div>

        <div class="text-center py-3 mt-4">
            <p class="mb-0">&copy; {{ currentYear }} Автошкола Политех. Все права защищены.</p>
        </div>
    </footer>
</template>

<script>
import axios from 'axios';
import API_ENDPOINTS from '@/services/api';

const FALLBACK = {
    phone: '+7-961-526-23-59',
    email: 'avtoshkola-politekh@mail.ru',
    address: 'г. Краснодар, р-н Табачной фабрики, ул. Спортивная, д. 2, к. Л.',
    logo_description:
        'Качественное обучение вождению для начинающих и профессионалов. Мы готовим водителей с гарантией успеха на дорогах.',
    documents: [],
    social: [
        {
            code: 'tg',
            url: 'https://t.me/kubstu_official',
            label: 'Telegram',
            icon_url: '/images/social/tg.png',
        },
        {
            code: 'vk',
            url: 'https://vk.com/kubstu_official',
            label: 'ВКонтакте',
            icon_url: '/images/social/vk.png',
        },
    ],
};

function digitsForTel(raw) {
    const d = String(raw || '').replace(/\D/g, '');
    if (d.length === 11 && d.startsWith('8')) {
        return '7' + d.slice(1);
    }
    if (d.length === 10) {
        return '7' + d;
    }
    return d;
}

export default {
    name: 'Footer',
    data() {
        return {
            logo: '/logo.png',
            currentYear: new Date().getFullYear(),
            payload: null,
        };
    },
    computed: {
        display() {
            return this.payload || FALLBACK;
        },
        telHref() {
            const d = digitsForTel(this.display.phone);
            return d ? `tel:+${d}` : '#';
        },
        mailtoHref() {
            const e = (this.display.email || '').trim();
            return e ? `mailto:${e}` : '#';
        },
    },
    mounted() {
        axios
            .get(API_ENDPOINTS.footer)
            .then((response) => {
                this.payload = response.data?.data ?? null;
            })
            .catch(() => {
                this.payload = null;
            });
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

.list-unstyled .fas {
    margin-right: 8px;
}

.footer-social-link {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin: 0 0.975rem;
    line-height: 1;
}

.footer-social-img {
    width: 36px;
    height: 36px;
    object-fit: contain;
    display: block;
    vertical-align: middle;
    border-radius: 0.75rem;
}

.bg-secondary {
    background-color: #343a40 !important;
}
</style>
