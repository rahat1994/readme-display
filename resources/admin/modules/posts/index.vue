<template>
  <div>
    <page-head :post="post" />
    <page-body :post="post" />
    <post-form :post="post" />
  </div>
</template>

<script type="text/javascript">
import PageHead from "./components/PageHeader";
import PageBody from "./components/PageBody";
import PostForm from "./components/Form";
import Post from "./Post";

export default {
  name: "Posts",
  components: {
    PageHead,
    PageBody,
    PostForm,
  },
  setup(props, ctx) {
    const post = Post.init();
    return {
      post: post,
      data: post.data,
    };
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
