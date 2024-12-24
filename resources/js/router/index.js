import { createRouter, createWebHistory } from 'vue-router'
import Home from '@/js/pages/Home.vue'
import Login from '@/js/pages/Login.vue'
import Quadrinho from '@/js/pages/Quadrinho.vue'
import Heroi from '@/js/pages/Heroi.vue'

const routes = [
    { path: '/', component: Home, meta: { requiresAuth: true } },
    { path: '/login', component: Login },
    { path: '/quadrinho', component: Quadrinho, meta: { requiresAuth: true } },
    { path: '/heroi', component: Heroi, meta: { requiresAuth: true } }
]

const router = createRouter({
    history: createWebHistory(),
    routes
})

router.beforeEach((to, from, next) => {
    const isAuthenticated = !!localStorage.getItem('authToken');
    if (to.meta.requiresAuth && !isAuthenticated) {
        next('/login');
    } else {
        next();
    }
});

export default router