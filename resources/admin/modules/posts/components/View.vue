<template>
	<div v-if="post.instance">
		<el-row>
			<el-col :span="4">
				<span class="label">Title : </span>
			</el-col>
			<el-col :span="20">
				<span>{{ post.instance.post_title }}</span>
			</el-col :span="12">
		</el-row>

		<hr class="hr" />

		<el-row>
			<el-col :span="4">
				<span class="label">Content : </span>
			</el-col>
			<el-col :span="20">
				<span>{{ post.instance.post_content }}</span>
			</el-col :span="12">
		</el-row>

		<hr class="hr" />

		<el-row>
			<el-col :span="4">
				<span class="label">Date : </span>
			</el-col>
			<el-col :span="20">
				<span>{{ post.instance.post_date }}</span>
			</el-col :span="12">
		</el-row>

		<hr class="hr" />

		<el-row>
			<el-col :span="4">
				<span class="label">Status : </span>
			</el-col>
			<el-col :span="20">
				<span>{{ post.instance.post_status }}</span>
			</el-col :span="12">
		</el-row>

		<div style="margin-top:50px;" v-if="post?.comments?.length">
			<hr class="hr" />
		
			<el-table :data="post?.comments">
				<el-table-column prop="comment_ID" label="ID" />
				<el-table-column prop="comment_date" label="Date" />
				<el-table-column prop="comment_author" label="Author" />
				<el-table-column label="Content">
					<template #default="scope">
						<div v-html="scope.row.comment_content"></div>
					</template>
				</el-table-column>
			</el-table>
		</div>
	</div>
</template>

<script type="text/javascript">
	export default {
		name: 'viewPost',
		props: ['post'],
		async created() {
			if (!this.post.instance) {
				this.post.find(this.$route.params.id);
				// const t = setInterval(() => {
				// 	this.post.instance = this.post.list.find(
				// 		p => p.ID == this.$route.params.id
				// 	); this.post.instance && clearInterval(t);	
				// }, 0);
			}
		}
	};
</script>

<style scoped>
	.hr {
		border: solid .5px #eee;
	}

	.label {
		font-weight:bold;
	}
</style>
