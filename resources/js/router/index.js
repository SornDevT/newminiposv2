// ສ້າງ route ແຍກການສະແດງຜົນ AppLayout ແລະ AuthLayout
import { createRouter, createWebHistory } from 'vue-router';
import AppLayout from '../layouts/AppLayout.vue';
import AuthLayout from '../layouts/AuthLayout.vue';

const routes = [
    { 
        path: '/',
        component: AppLayout,
        meta: { requiresAuth: true }, // Add this meta field
        children: [
            // import ໜ້າຕ່າງໆເຂົ້າມາໃນ AppLayout : 
            {
                path: '', // Default path for '/'
                name: 'Dashboard',
                component: () => import('../pages/Dashboard.vue')
            },
            {
                path: 'products',
                name: 'Products',
                component: () => import('../pages/Product.vue')
            },
            {
                path: 'pos',
                name: 'POS',
                component: () => import('../pages/Pos.vue')
            },
            {
                path: 'reports',
                name: 'Reports',
                component: () => import('../pages/Report.vue')
            },
            {
                path: 'stock-import',
                name: 'StockImport',
                component: () => import('../pages/StockImport.vue')
            },
            {
                path: 'settings/categories-unit',
                name: 'CategoriesUnit',
                component: () => import('../pages/settings/CategoriesUnit.vue')
            },
            {
                path: 'settings/users',
                name: 'Users',
                component: () => import('../pages/settings/Users.vue')
            },
            {
                path: 'customers',
                name: 'Customers',
                component: () => import('../pages/Customer.vue')
            },
            {
                path: 'suppliers',
                name: 'Suppliers',
                component: () => import('../pages/Supplier.vue')
            },
            {
                path: 'expenses',
                name: 'Expenses',
                component: () => import('../pages/Expense.vue')
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
                name: 'Login',
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

// Navigation Guard
router.beforeEach((to, from, next) => {
    const token = localStorage.getItem('token');
  
    if (to.meta.requiresAuth && !token) {
      // If route requires auth and no token is found, redirect to login
      next({ name: 'Login' });
    } else if (to.name === 'Login' && token) {
      // If user is logged in and tries to access login page, redirect to dashboard
      next({ name: 'Dashboard' });
    } else {
      // Otherwise, allow navigation
      next();
    }
  });

export default router;