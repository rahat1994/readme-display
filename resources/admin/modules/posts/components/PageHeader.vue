<template>
	<div class="page-header">
		<el-row>
            <el-col :span="12">
            	<strong v-if="!$route.params.id">Posts</strong>
            	<strong v-else>Post - {{ $route.params.id }}</strong>
            </el-col>

            <el-col :span="12" style="text-align:right">
            	<div v-if="$route.name === 'posts'">
	            	<el-button
	            		link
	            		type="primary"
	            		@click="post.showForm"
	            		class="el-create-button"
	            	>Create</el-button>
            		<post-status-filter :post="post" style="float:right" />
            	</div>
            	<div v-else>
            		<el-button
            			link type="primary"
            			@click="$router.back()"
            		>&larr; Back To Posts</el-button>
            	</div>

            </el-col>
        </el-row>
	</div>
</template>

<script type="text/javascript">
	import PostStatusFilter from './PostStatusFilter';

	export default {
		name: 'PageHeader',
		props: ['post'],
		components: { PostStatusFilter }
	};
</script>

<style scoped>
	.page-header {
		border-bottom:solid 1px #eee;
		padding:10px 20px 5px;
		margin-bottom:0px;
		font-size: 18px;
		background:#fff;
	}
	.el-create-button {
		float:right;
		margin-top:5px;
	}
</style>
