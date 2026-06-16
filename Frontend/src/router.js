import { createRouter, createWebHistory } from 'vue-router'
import Order from "../views/Order.vue";

const routes = [
    {
        path: '/order',
        name: 'order',
        component: Order,
    }
]

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes
})

export default router