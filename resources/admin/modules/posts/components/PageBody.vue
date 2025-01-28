<template>
  <div class="page-content clearfix">
    <div v-if="$route.name === 'posts'">
      <el-table :data="post.list" v-loading="post.isBusy.loading">
        <el-table-column label="ID" prop="ID" />
        <el-table-column label="Title" prop="post_title" />
        <el-table-column label="Status" prop="post_status" />
        <el-table-column label="Created At">
          <template #default="scope">
            {{ scope.row.post_date }}
          </template>
        </el-table-column>
        <el-table-column label="Actions">
          <template #default="scope">
            <el-button link type="info" @click="view(scope.row)"
              >View</el-button
            >

            <el-button link type="primary" @click="post.edit(scope.row)"
              >Edit</el-button
            >

            <confirm #reference @yes="post.delete(scope.row)">
              <el-button link type="danger">Delete</el-button>
            </confirm>
          </template>
        </el-table-column>
      </el-table>

      <div class="pagination">
        <Pagination :pagination="post.pagination" @fetch="redirect" />
      </div>
    </div>
    <div v-if="$route.name === 'posts.view'">
      <router-view :post="post" />
    </div>
  </div>
</template>

<script type="text/javascript">
import View from "@/modules/posts/components/View";
import Pagination from "@/components/Pagination";
import Confirm from "@/components/Confirm";

export default {
  name: "PageBody",
  components: { View, Pagination, Confirm },
  props: ["post"],
  created() {
    this.post.get();
  },
  methods: {
    redirect() {
      this.$router.push({
        name: "posts",
        query: {
          ...this.post.useQuery(),
        },
      });
    },
    view(post) {
      this.post.instance = post;

      this.$router.push({
        name: "posts.view",
        params: { id: post.ID },
      });
    },
  },
};
</script>

<style scoped>
.pagination {
  float: right;
  clear: both;
  margin-top: 30px;
}

.clearfix::after {
  content: "";
  display: table;
  clear: both;
}
</style>
