// ສ້າງ route ແຍກການສະແດງຜົນ AppLayout ແລະ AuthLayout
import { createRouter, createWebHistory } from 'vue-router';
import AppLayout from '../layouts/AppLayout.vue';
import AuthLayout from '../layouts/AuthLayout.vue';

const routes = [
    { 
        path: '/',
        component: AppLayout,
        children: [
            // import ໜ້າຕ່າງໆເຂົ້າມາໃນ AppLayout : 
            {
                path: '/',
                component: () => import('../pages/Dashboard.vue')
            },
            {
                path: '/products',
                component: () => import('../pages/Product.vue')
            },
            {
                path: '/pos',
                component: () => import('../pages/Pos.vue')
            },
            {
                path: '/reports',
                component: () => import('../pages/Report.vue')
            },
            {
                path: '/stock-import',
                component: () => import('../pages/StockImport.vue')
            },
            {
                path: '/settings/categories-unit',
                component: () => import('../pages/settings/CategoriesUnit.vue')
            },
            {
                path: '/settings/users',
                component: () => import('../pages/settings/Users.vue')
            }
        ]
    },
    { 
        path: '/auth',
        component: AuthLayout,
        children: [
            // import ໜ້າຕ່າງໆເຂົ້າມາໃນ AuthLayout :
            {
                path: 'login',
                component: () => import('../pages/Auth/Login.vue')
            }
        ]
    },
    {
        path: '/:pathMatch(.*)*',
        redirect: '/'
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;