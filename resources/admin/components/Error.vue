<template>
    <span v-if="field" class="el-form-item__error">
        {{ Object.values(field).shift() }}
    </span>
</template>

<script>
    export default {
        name: 'Error',
        props: ['field']
    };
</script>
