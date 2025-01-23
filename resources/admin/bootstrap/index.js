import { createApp } from 'vue';
import router from '@/router';
import mixins from '@/mixins';
import Rest from '@/utils/http/Rest.js';
import controllers from './controllers';
import Application from "@/components/Application";

if (typeof __webpack_public_path__ !== 'undefined') {
    if (fluentFrameworkAdmin && fluentFrameworkAdmin.asset_url) {
        __webpack_public_path__ = fluentFrameworkAdmin.asset_url;
    }
}

const app = createApp(Application);

mixins.forEach(mixinObject => app.mixin(mixinObject));

app.config.globalProperties.$controllers = controllers;

app.config.globalProperties.appVars = fluentFrameworkAdmin;

app.use(router(app));

Rest.setInstance(app);

window.fluentFrameworkAdmin = {};

export default app;
