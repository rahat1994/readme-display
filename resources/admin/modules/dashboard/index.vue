<template>
    <transition name="fade" mode="out-in" appear>
        <div>
            <div class="page-header">
                <el-row>
                    <el-col :span="12">
                        <strong>{{ title }}</strong>
                    </el-col>
                </el-row>
            </div>

            <div class="page-content">Welcome to fluent framework...</div>
        </div>
    </transition>
</template>

<script type="text/javascript">
export default {
    name: "Dashboard",
    data() {
        return {
            title: "Dashboard",
        };
    },
};
</script>

<style scoped>
.page-header {
    border-bottom: solid 1px #eee;
    padding: 10px 20px 15px;
    margin-bottom: 0px;
    font-size: 18px;
    background: #fff;
}

.page-content {
    margin-bottom: 10px;
    font-size: 15px;
    padding: 10px 20px 15px;
}
</style>
