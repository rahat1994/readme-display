<template>
	<transition name="fade" mode="out-in" appear>
		<div class="page-content" style="height:75vh;">
			<el-row justify="space-evenly">
	    		<el-col :sm="12" :lg="6">
	      			<el-result
	        			icon="error"
	        			title="404"
	        			sub-title="Oops! You are lost."
	      			>
	        			<template #extra>
	          				<el-button
	          					type="primary"
	          					@click="$router.push({ name: 'dashboard' })"
	          				>Let's Go Home</el-button>
	        			</template>
	      			</el-result>
	    		</el-col>
	  		</el-row>
		</div>
	</transition>
</template>

<script type="text/javascript">
	export default {
		name: 'NotFound'
	};
</script>
