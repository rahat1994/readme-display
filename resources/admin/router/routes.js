export default [
    {
        path: "/",
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
