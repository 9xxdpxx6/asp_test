import { createRouter, createWebHistory } from 'vue-router'

const routes = [
    {
        path: '/',
        name: 'home',
        component: () => import('@/components/Home.vue'),
        meta: { title: 'Автошкола Политех — Обучение вождению в Краснодаре' },
    },
    {
        path: '/about',
        name: 'about',
        component: () => import('@/components/About.vue'),
        meta: { title: 'О нас — Автошкола Политех' },
    },
    {
        path: '/contacts',
        name: 'contacts',
        component: () => import('@/components/Contacts.vue'),
        meta: { title: 'Контакты — Автошкола Политех' },
    },
    {
        path: '/prices',
        name: 'prices',
        component: () => import('@/components/Prices.vue'),
        meta: { title: 'Цены на обучение — Автошкола Политех' },
    },
    {
        path: '/prices/:id',
        name: 'category',
        component: () => import('@/components/CategoryDetail.vue'),
        meta: { title: 'Категория — Автошкола Политех' },
    },
    {
        path: '/blog',
        name: 'blog',
        component: () => import('@/components/Blog.vue'),
        meta: { title: 'Новости — Автошкола Политех' },
    },
    {
        path: '/blog/:slug',
        name: 'post',
        component: () => import('@/components/Post.vue'),
        meta: { title: 'Новость — Автошкола Политех' },
    },
    {
        path: '/discounts',
        name: 'discounts',
        component: () => import('@/components/Discounts.vue'),
        meta: { title: 'Скидки и акции — Автошкола Политех' },
    },
    {
        path: '/discounts/:id',
        name: 'discount',
        component: () => import('@/components/DiscountDetail.vue'),
        meta: { title: 'Скидка — Автошкола Политех' },
    },
    {
        path: '/:pathMatch(.*)*',
        name: 'not-found',
        component: () => import('@/components/Error.vue'),
        meta: { title: 'Страница не найдена — Автошкола Политех' },
    },
]

const router = createRouter({
    history: createWebHistory(),
    routes,
    scrollBehavior(to, from, savedPosition) {
        if (savedPosition) {
            return savedPosition;
        }
        return { top: 0 };
    },
})

router.afterEach((to) => {
    document.title = to.meta.title || 'Автошкола Политех';
})

export default router
