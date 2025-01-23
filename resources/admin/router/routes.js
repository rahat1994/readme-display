export default [
    {
        path: '/',
        name: 'dashboard',
        component: () => import('@/modules/dashboard'),
        meta: {
            active_menu: 'dashboard'
        }
    },
    {
        path: '/posts',
        name: 'posts',
        component: () => import('@/modules/posts'),
        meta: {
            active_menu: 'posts'
        },
        children: [
            {
                props: true,
                path: ':id/view',
                name: 'posts.view',
                component: () => import('@/modules/posts/components/View'),
            }
        ]
    },
    {
        path: '/:pathMatch(.*)*',
        name: 'NotFound',
        component: () => import('@/components/NotFound'),
    }
];
