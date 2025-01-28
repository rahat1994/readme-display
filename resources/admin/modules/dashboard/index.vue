<template>
  <el-row>
    <el-col :span="18">
      <div class="grid-content ep-bg-purple-dark" />
    </el-col>
    <el-col :span="6">
      <div class="grid-content ep-bg-purple-dark" align="end">
        <el-button @click="dialogFormVisible = true" type="success" :icon="Edit"
          >Add New</el-button
        >
      </div>
    </el-col>
  </el-row>
  <el-table :data="filterTableData" style="width: 100%">
    <el-table-column label="Name" prop="name" />
    <el-table-column label="ShortCode" prop="slug">
      <template #default="scope">
        <code>[readme-display plugin="{{ scope.row.slug }}"]</code>
      </template>
    </el-table-column>
    <el-table-column label="Action" align="center">
      <template #default="scope">
        <el-button
          size="small"
          type="danger"
          @click="handleDelete(scope.$index, scope.row)"
        >
          Delete
        </el-button>
      </template>
    </el-table-column>
  </el-table>

  <el-dialog v-model="dialogFormVisible" title="Shipping address" width="500">
    <el-form @submit="onSubmit">
      <el-form-item
        v-bind="nameProps"
        :label="$t('Plugin Name')"
        :label-width="formLabelWidth"
      >
        <el-input v-model="name" autocomplete="off" />
      </el-form-item>

      <el-form-item
        v-bind="slugProps"
        :label="$t('Plugin Slug')"
        :label-width="formLabelWidth"
      >
        <el-input v-model="slug" autocomplete="off" />
      </el-form-item>

      <div style="text-align: end">
        <el-button @click="resetForm()">Reset</el-button>
        <el-button type="primary" native-type="submit"> Confirm </el-button>
      </div>
    </el-form>
    <template #footer> </template>
  </el-dialog>
</template>

<script type="text/javascript" setup>
import { useForm } from "vee-validate";
import * as zod from "zod";
import { toTypedSchema } from "@vee-validate/zod";
import { computed, ref, reactive, getCurrentInstance, onMounted } from "vue";
import { Edit } from "@element-plus/icons-vue";
const search = ref("");
const form = reactive({
  pluginSlug: "",
});

const schema = toTypedSchema(
  zod.object({
    name: zod.string().min(5),
    slug: zod.string().min(5),
  })
);

const formLabelWidth = "140px";
const dialogFormVisible = ref(false);
const filterTableData = computed(() =>
  tableData.filter(
    (data) =>
      !search.value ||
      data.name.toLowerCase().includes(search.value.toLowerCase())
  )
);

const { defineField, handleSubmit, resetForm, errors } = useForm({
  validationSchema: schema,
});

const elPlusConfig = (state) => ({
  props: {
    validateEvent: false,
    error: state.errors[0],
    required: state.required,
  },
});

const [name, nameProps] = defineField("name", elPlusConfig);
const [slug, slugProps] = defineField("slug", elPlusConfig);

const ctx = getCurrentInstance().ctx;

const onSubmit = handleSubmit((values) => {
  console.log(values);
});

const handleDelete = (index, row) => {
  console.log(index, row);
};

const tableData = [
  {
    name: "Social Ninja",
    slug: "social-ninja",
    createdAt: "2021-09-01",
  },
  {
    name: "Fluent Forms",
    slug: "fluent-forms",
    creqtatedAt: "2021-09-01",
  },
  {
    name: "Fluent CRM",
    slug: "fluent-crm",
    createdAt: "2021-09-01",
  },
];

onMounted(() => {
  console.log("mounted");
  // ctx.$get("readmedisplay");
});
</script>

<style scoped></style>
