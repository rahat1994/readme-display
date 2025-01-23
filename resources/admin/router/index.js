import { createRouter, createWebHashHistory } from 'vue-router';
import routes from '@/router/routes';

export default function (app) {
    
    const router = createRouter({
        history: createWebHashHistory(),
        strict: true,
        routes,
    });

    router.afterEach((to, from) => {
        const activeMenu = to.meta.active_menu;

        if(!activeMenu) return;
        
        const slug = app.config.globalProperties.appVars.slug;

        document.querySelectorAll(
            '.fframe_menu li'
        ).forEach(item => item.classList.remove('active_item'));

        document.querySelector(
            `.fframe_menu li.fframe_item_${activeMenu}`
        ).classList.add('active_item');

        document.querySelectorAll(
            `.toplevel_page_${slug} li`
        ).forEach(item => item.classList.remove('current'));

        const element = document.querySelector(
            `#toplevel_page_${slug}`
        );
        
        if (element) {
            element.classList.add('current');
        }

        if (to.meta.title) {
            document.querySelector(
                'head title'
            ).textContent = `${to.meta.title} - Fluent Framework`;
        }
    });

    return router;
};
