import { createRouter, createWebHistory } from 'vue-router'
import SnapPopUpPage from '../views/SnapPopUpPage.vue'

const routes = [
    {
        path: '/snap-pop-up',
        name: 'snap-pop-up',
        component: SnapPopUpPage
    }
]

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes
})

export default router