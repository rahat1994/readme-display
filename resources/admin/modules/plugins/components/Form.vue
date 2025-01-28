<template>
    <div>
        <el-dialog
            :title="title"
            :destroy-on-close="true"
            :close-on-click-modal="false"
            @closed="plugin.hideForm"
            v-model="plugin.isVisible.form"
        >
            <el-form :model="plugin.form" label-width="auto">
                <el-form-item label="Name">
                    <el-input v-model="plugin.form.name" type="text" />
                    <error :field="plugin.errors.name" />
                </el-form-item>

                <el-form-item label="Slug">
                    <el-input v-model="plugin.form.slug" type="text" />
                    <error :field="plugin.errors.slug" />
                </el-form-item>
            </el-form>

            <template #footer>
                <div class="dialog-footer">
                    <el-button @click="plugin.hideForm">Close</el-button>

                    <el-button
                        type="primary"
                        @click="save"
                        :loading="plugin.isBusy.saving"
                        >Save</el-button
                    >
                </div>
            </template>
        </el-dialog>
    </div>
</template>

<script type="text/javascript">
import Error from "@/components/Error";

export default {
    name: "PluginForm",
    components: { Error },
    props: ["plugin"],
    methods: {
        async save() {
            console.log(this.plugin.form.name);
            try {
                this.plugin.isBusy.saving = true;
                await this.plugin.save();
                this.$notifySuccess("Plugin has been saved successfully.");
            } catch (e) {
                if (e.status == 422) {
                    this.plugin.errors = e.errors;
                } else throw e;
            } finally {
                this.plugin.isBusy.saving = false;
            }
        },
    },
    computed: {
        title() {
            return this.plugin.form.id ? "Edit Plugin" : "Create Plugin";
        },
    },
};
</script>
