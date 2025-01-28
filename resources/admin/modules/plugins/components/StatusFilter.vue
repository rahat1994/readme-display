<template>
	<div class="post-filter-container">
		<span class="post-filter-label">Filter</span>
		<el-select
			v-model="post.status"
			class="post-filter-select"
			@change="get"
		>
	        <el-option label="All" value="all" />
	        <el-option label="Draft" value="draft" />
	        <el-option label="Pending" value="pending" />
	        <el-option label="Published" value="publish" />
	    </el-select>
	</div>
</template>

<script type="text/javascript">
	export default {
		name: 'PostStatusFilter',
		props: ['post'],
		methods: {
			get() {
				this.post.pagination.per_page = 2;
				this.post.pagination.current_page = 1;
				this.$router.push({
					name: 'posts',
					query: {
						...this.post.useQuery()
					}
				});
			}
		}
	};
</script>

<style scoped>
	.post-filter-container {
		max-width: 300px;
	}
	.post-filter-label {
		font-size:14px;
		margin-right:10px;
	}
	.post-filter-select {
		width:200px;
		margin-right:20px;
	}
</style>
