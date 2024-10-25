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
        path: '/posts/{post}',
        component: () => import('./components/Post.vue')
    },
]

const router = createRouter({
    history: createWebHistory(),
    routes,
})

export default router
