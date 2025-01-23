<template>
    <el-popover
        width="170"
        @hide="cancel"
        v-model:visible="visible"
        :placement="placement"
        trigger="click"
    >
        <p v-html="message"></p>

        <div class="action-buttons">
            <el-button
                link
                size="small"
                @click="cancel">
                cancel
            </el-button>

            <el-button
                type="primary"
                size="small"
                @click="confirm">
                confirm
            </el-button>
        </div>

        <template #reference>
            <slot name="reference">
                <i class="el-icon-delete"/>
            </slot>
        </template>
    </el-popover>
</template>

<script>
export default {
    name: 'Confirm',
    props: {
        placement: {
            type: String,
            default: 'top-end'
        },
        message: {
            type: String,
            default: 'Are you sure?'
        }
    },
    data() {
        return {
            visible: false
        }
    },
    methods: {
        hide() {
            this.visible = false;
        },
        confirm() {
            this.hide();
            this.$emit('yes');
        },
        cancel() {
            this.hide();
            this.$emit('no');
        }
    }
};
</script>
