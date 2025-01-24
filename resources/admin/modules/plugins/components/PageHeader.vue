<template>
  <div class="page-header">
    <el-row>
      <el-col :span="12">
        <strong v-if="!$route.params.id">Plugins</strong>
        <strong v-else>Plugin - {{ $route.params.name }}</strong>
      </el-col>

      <el-col :span="12" style="text-align: right">
        <div v-if="$route.name === 'plugins'">
          <el-button
            link
            type="primary"
            @click="plugin.showForm"
            class="el-create-button"
            >Add new plugin</el-button
          >
        </div>
        <div v-else>
          <el-button link type="primary" @click="$router.back()"
            >&larr; Back To Posts</el-button
          >
        </div>
      </el-col>
    </el-row>
  </div>
</template>

<script type="text/javascript">
export default {
  name: "PageHeader",
  props: ["plugin"],
  components: {},
};
</script>

<style scoped>
.page-header {
  border-bottom: solid 1px #eee;
  padding: 10px 20px 5px;
  margin-bottom: 0px;
  font-size: 18px;
  background: #fff;
}
.el-create-button {
  float: right;
  margin-top: 5px;
}
</style>
