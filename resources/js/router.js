import { createRouter, createWebHistory } from 'vue-router'

const routes = [
    {
        path: '/',
        name: 'home',
        component: () => import('./components/Home.vue'),
    },
    // {
    //     path: '/about',
    //     name: 'about',
    //     component: () => import('./components/About.vue'),
    // },
    {
        path: '/contacts',
        name: 'contacts',
        component: () => import('./components/Contacts.vue'),
    },
    {
        path: '/prices',
        name: 'prices',
        component: () => import('./components/Prices.vue'),
    },
    {
        path: '/prices/:id',
        name: 'category',
        component: () => import('./components/CategoryDetail.vue'),
    },
    {
        path: '/blog',
        name: 'blog',
        component: () => import('./components/Blog.vue'),
    },
    // {
    //     path: '/blog/:id',
    //     name: 'post',
    //     component: () => import('./components/Post.vue'),
    // },
    {
        path: '/blog/:slug',
        name: 'post',
        component: () => import('./components/Post.vue'),
    },
    {
        path: '/discounts',
        name: 'discounts',
        component: () => import('./components/Discounts.vue'),
    },
    {
        path: '/discounts/:id',
        name: 'discount',
        component: () => import('./components/DiscountDetail.vue'),
    },
    // страница ошибки
    {
        path: '/:pathMatch(.*)*',
        name: 'not-found',
        component: () => import('./components/Error.vue'),
    },
]

const router = createRouter({
    history: createWebHistory(),
    routes,
})

export default router
