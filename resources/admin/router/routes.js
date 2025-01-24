export default [
    {
        path: "/",
        name: "dashboard",
        component: () => import("@/modules/dashboard"),
        meta: {
            active_menu: "dashboard",
        },
    },
    {
        path: "/posts",
        name: "posts",
        component: () => import("@/modules/posts"),
        meta: {
            active_menu: "posts",
        },
        children: [
            {
                props: true,
                path: ":id/view",
                name: "posts.view",
                component: () => import("@/modules/posts/components/View"),
            },
        ],
    },
    {
        path: "/plugins",
        name: "plugins",
        component: () => import("@/modules/plugins"),
        meta: {
            active_menu: "plugins",
        },
        children: [
            {
                props: true,
                path: ":id/view",
                name: "plugins.view",
                component: () => import("@/modules/plugins/components/View"),
            },
        ],
    },
    {
        path: "/:pathMatch(.*)*",
        name: "NotFound",
        component: () => import("@/components/NotFound"),
    },
];
