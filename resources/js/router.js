import { createRouter, createWebHistory } from 'vue-router'
// import About from './components/About.vue'

const routes = [
    {
        path: '/',
        component: () => import('./components/Home.vue')
    },
    {
        path: '/about',
        component: () => import('./components/About.vue')
    },
    {
        path: '/contacts',
        component: () => import('./components/Contacts.vue')
    },
    {
        path: '/prices',
        component: () => import('./components/Prices.vue')
    },
    {
        path: '/prices/:id',
        name: 'category',
        component: () => import('./components/CategoryDetail.vue')
    },
    {
        path: '/blog',
        component: () => import('./components/Blog.vue')
    },
    {
        path: '/blog/:id',
        component: () => import('./components/Post.vue')
    },

    {
        path: '/:pathMatch(.*)*',
        name: 'NotFound',
        component: () => import('./components/Error.vue')
    },
]

const router = createRouter({
    history: createWebHistory(),
    routes,
})

export default router
