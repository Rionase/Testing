import { createRouter, createWebHistory } from 'vue-router'
import Order from "../views/Order.vue";
import AddOrder from "../views/AddOrder.vue";

const routes = [
    {
        path: '/order',
        name: 'order',
        component: Order,
    },
    {
        path: '/order/add',
        name: 'addOrder',
        component: AddOrder,
    }
]

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes
})

export default router