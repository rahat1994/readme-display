<template>
    <div class="page-content clearfix">
        <div v-if="$route.name === 'plugins'">
            <el-table
                :data="plugin.list"
                v-loading="plugin.isBusy.loading"
                stripe
            >
                <el-table-column label="ID" prop="id" width="55" />
                <el-table-column label="Name" prop="name" />
                <el-table-column label="Short Code" prop="slug">
                    <template #default="scope">
                        <code
                            >[readme-display plugin="{{
                                scope.row.slug
                            }}"]</code
                        >
                    </template>
                </el-table-column>
                <el-table-column label="Actions">
                    <template #default="scope">
                        <el-button link type="info" @click="view(scope.row)"
                            >View</el-button
                        >

                        <el-button
                            link
                            type="primary"
                            @click="plugin.edit(scope.row)"
                            >Edit</el-button
                        >

                        <confirm #reference @yes="plugin.delete(scope.row)">
                            <el-button link type="danger">Delete</el-button>
                        </confirm>
                    </template>
                </el-table-column>
            </el-table>

            <div class="pagination">
                <Pagination :pagination="plugin.pagination" @fetch="redirect" />
            </div>
        </div>
        <div v-if="$route.name === 'plugins.view'">
            <router-view :plugin="plugin" />
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
    props: ["plugin"],
    created() {
        console.log(this.plugin);
        this.plugin.get();
    },
    methods: {
        redirect() {
            this.$router.push({
                name: "plugins",
                query: {
                    ...this.plugin.useQuery(),
                },
            });
        },
        view(plugin) {
            this.plugin.instance = plugin;
            console.log("Plugin", plugin);
            this.$router.push({
                name: "plugins.view",
                params: { id: plugin.id },
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
