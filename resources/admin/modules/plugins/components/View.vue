<template>
  <p v-if="plugin.isBusy.loading">loading</p>
  <span v-if="plugin.instance">
    {{ plugin.instance.id }}
  </span>
</template>

<script type="text/javascript">
export default {
  name: "viewPlugin",
  props: ["plugin"],
  async created() {
    console.log(this.plugin.instance);
    if (!this.plugin.instance) {
      this.plugin.find(this.$route.params.id);
      console.log(this.plugin.instance);
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
