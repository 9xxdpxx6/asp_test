<template>
    <section class="section-spacing">
        <div class="container" v-show="pageReady">
            <div class="text-center mb-5">
                <h1 class="display-6 section-title">{{ cfg.page_title }}</h1>
                <p v-if="cfg.page_subtitle" class="text-muted mt-3">{{ cfg.page_subtitle }}</p>
            </div>

            <div
                v-for="(branch, idx) in cfg.branches"
                :key="branch.id || 'b-' + idx"
                class="contact-branch-block mb-5"
            >
                <div
                    v-if="branch.map_embed_html && branch.map_embed_html.trim()"
                    class="map-container mb-4 rounded-3 overflow-hidden contact-map-embed"
                    v-html="branch.map_embed_html"
                />
                <h3 class="mb-3">{{ branch.title }}</h3>
                <div v-if="branch.photos && branch.photos.length" class="row g-3 mb-4">
                    <div
                        v-for="(src, pi) in branch.photos"
                        :key="pi"
                        :class="photoColClass(branch.photos.length)"
                    >
                        <img
                            :src="src"
                            :alt="branch.title"
                            class="img-fluid rounded-3 w-100 contact-branch-photo"
                        />
                    </div>
                </div>
                <div
                    v-if="branch.details_text && branch.details_text.trim()"
                    class="mb-4 lead contact-details-text"
                    v-html="branch.details_text"
                />
            </div>

            <div class="mb-4 lead contact-global-block">
                <h3>{{ cfg.contacts_heading }}</h3>
                <p v-if="cfg.contacts_intro">{{ cfg.contacts_intro }}</p>
                <ul class="list-unstyled contact-contact-lines">
                    <li v-for="(p, i) in cfg.phones" :key="'ph-' + i">
                        <strong>{{ phoneLabel(i) }}:</strong><span class="contact-line-gap" aria-hidden="true"></span>
                        <a :href="telHref(p)">{{ p }}</a>
                    </li>
                    <li v-for="(e, i) in cfg.emails" :key="'em-' + i">
                        <strong>{{ emailLabel(i) }}:</strong><span class="contact-line-gap" aria-hidden="true"></span>
                        <a :href="'mailto:' + e">{{ e }}</a>
                    </li>
                </ul>
            </div>
        </div>
    </section>
</template>

<script>
import axios from 'axios';
import API_ENDPOINTS from '@/services/api';

const EMPTY = {
    page_title: 'Контакты',
    page_subtitle: '',
    contacts_heading: 'Свяжитесь с нами',
    contacts_intro: '',
    phones: [],
    emails: [],
    branches: [],
};

export default {
    name: 'Contacts',
    data() {
        const hasBoot =
            typeof window !== 'undefined' && window.__INITIAL_CONTACT__ != null;
        return {
            payload: hasBoot ? window.__INITIAL_CONTACT__ : null,
            pageReady: hasBoot,
        };
    },
    computed: {
        cfg() {
            if (this.payload && typeof this.payload === 'object') {
                return this.payload;
            }
            return EMPTY;
        },
    },
    mounted() {
        if (this.pageReady) {
            return;
        }
        axios
            .get(API_ENDPOINTS.contactsPage)
            .then((res) => {
                const data = res.data?.data ?? res.data;
                this.payload = data && typeof data === 'object' ? data : EMPTY;
            })
            .catch(() => {
                this.payload = EMPTY;
            })
            .finally(() => {
                this.pageReady = true;
            });
    },
    methods: {
        photoColClass(n) {
            if (n <= 1) {
                return 'col-12';
            }
            if (n === 2) {
                return 'col-md-6';
            }
            return 'col-md-4';
        },
        telHref(phone) {
            return 'tel:' + String(phone).replace(/\s/g, '');
        },
        phoneLabel(index) {
            return index === 0 ? 'Телефон' : 'Доп. телефон';
        },
        emailLabel(index) {
            return index === 0 ? 'Электронная почта' : 'Доп. e-mail';
        },
    },
};
</script>

<style scoped>
.map-container {
    width: 100%;
    min-height: 320px;
}

.contact-map-embed :deep(iframe) {
    width: 100% !important;
    min-height: 400px;
    border: 0;
    display: block;
}

.contact-branch-photo {
    object-fit: cover;
    max-height: 520px;
}

.contact-details-text :deep(strong) {
    font-weight: 600;
}

.contact-contact-lines .contact-line-gap {
    display: inline-block;
    width: 0.4rem;
}

@media (max-width: 767.98px) {
    .contact-branch-block .col-md-4,
    .contact-branch-block .col-md-6 {
        flex: 0 0 100%;
        max-width: 100%;
    }
}
</style>
