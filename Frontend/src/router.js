import { createRouter, createWebHistory } from 'vue-router'
import Product from "../views/Product.vue";
import Order from "../views/Order.vue";
import AddOrder from "../views/AddOrder.vue";
import OrderDetail from "../views/OrderDetail.vue";

const routes = [
    {
        path: '/product',
        name: 'product',
        component: Product,
    },
    {
        path: '/order',
        name: 'order',
        component: Order,
    },
    {
        path: '/order/add',
        name: 'addOrder',
        component: AddOrder,
    },
    {
        path: '/order/:id',
        name: 'orderDetail',
        component: OrderDetail
    }
]

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes
})

export default router