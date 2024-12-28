import { createRouter, createWebHistory } from 'vue-router'
import Home from '@/js/pages/Home.vue'
import Login from '@/js/pages/Login.vue'
import Comics from '@/js/pages/Comics.vue'
import Characters from '@/js/pages/Characters.vue'
import Favorites from '@/js/pages/Favorites.vue'
import User from '@/js/pages/User.vue'

const routes = [
    { path: '/', component: Home, meta: { requiresAuth: true } },
    { path: '/login', component: Login },
    { path: '/comics', component: Comics, meta: { requiresAuth: true } },
    { path: '/characters', component: Characters, meta: { requiresAuth: true } },
    { path: '/favorites', component: Favorites, meta: { requiresAuth: true } },
    { path: '/user', component: User, meta: { requiresAuth: true } }
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