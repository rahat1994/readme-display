<template>
	<div>
		<el-dialog
			:title="title"
			:destroy-on-close="true"
			:close-on-click-modal="false"
			@closed="post.hideForm"
			v-model="post.isVisible.form"
		>
			<el-form :model="post.form" label-width="auto">
				<el-form-item label="Title">
					<el-input v-model="post.form.post_title" type="text" />
					<error :field="post.errors.post_title" />
				</el-form-item>

				<el-form-item label="Content">
					<el-input v-model="post.form.post_content" type="textarea" />
					<error :field="post.errors.post_content" />
				</el-form-item>
			</el-form>

			<template #footer>
		      <div class="dialog-footer">
		        <el-button @click="post.hideForm">Close</el-button>
		        
		        <el-button
		        	type="primary"
		        	@click="save"
		        	:loading="post.isBusy.saving"
		        >Save</el-button>
		      </div>
		    </template>
		</el-dialog>
	</div>
</template>

<script type="text/javascript">
	import Error from '@/components/Error';

	export default {
		name: 'PostForm',
		components: { Error },
		props: ['post'],
		methods: {
			async save() {
				try {
		            this.post.isBusy.saving = true;
		            await this.post.save();
		            this.$notifySuccess('Post has been saved successfully.');
				} catch (e) {
		            if (e.status == 422) {
		                this.post.errors = e.errors;
		            } else throw e;
				} finally {
		            this.post.isBusy.saving = false;
		        }
			}
		},
		computed: {
			title() {
				return this.post.form.ID ? 'Edit Post' : 'Create Post';
			}
		}
	};
</script>
