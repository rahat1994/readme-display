<template>
    <div>
        <PageHeader :plugin="plugin" />
        <PageBody :plugin="plugin" />
        <PageForm :plugin="plugin" />
    </div>
</template>

<script type="text/javascript">
import Plugin from "./Plugin";
import PageBody from "./components/PageBody";
import PageHeader from "./components/PageHeader";
import PageForm from "./components/Form";

export default {
    name: "Plugin",
    components: {
        PageBody,
        PageHeader,
        PageForm,
    },
    setup(props, ctx) {
        const plugin = Plugin.init();
        return {
            plugin: plugin,
            data: plugin.data,
        };
    },
    onMounted() {
        console.log("Plugin mounted");
    },
};
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.5s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
