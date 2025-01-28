<template>
    <p v-if="plugin.isBusy.loading">loading</p>
    <span v-if="plugin.instance">
        <h4>{{ plugin.instance.name }}</h4>
        <div>
            <span class="label">Shortcode:</span>
            <span>
                <code>
                    [readme-display plugin="{{ plugin.instance.slug }}"]</code
                >
            </span>
        </div>
    </span>
</template>

<script type="text/javascript">
export default {
    name: "viewPlugin",
    props: ["plugin"],
    async created() {
        if (!this.plugin.instance) {
            this.plugin.find(this.$route.params.id);
            // const t = setInterval(() => {
            // 	this.post.instance = this.post.list.find(
            // 		p => p.ID == this.$route.params.id
            // 	); this.post.instance && clearInterval(t);
            // }, 0);
        }
    },
    data() {
        return {
            data: {
                name: "Plugin Name",
                description: "Plugin Description",
                slug: "plugin-slug",
            },
        };
    },
};
</script>

<style scoped>
.hr {
    border: solid 0.5px #eee;
}

.label {
    font-weight: bold;
}
</style>
