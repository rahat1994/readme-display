<template>
	<el-button
		:disabled="!this.pagination.prev_page_url"
		@click="changePage('prev_page_url')"
	>Prev</el-button>
	<el-button
		:disabled="!this.pagination.next_page_url"
		@click="changePage('next_page_url')"
	>Next</el-button>
</template>

<script type="text/javascript">
	export default {
		name: 'CursorPagination',
		emits: ["fetch"],
		props: {
	        pagination: {
	            required: true,
	            type: Object
	        }
	    },
		methods: {
			changePage(which) {
				let cursor = null;
				
				let page = this.pagination[which];

				if (page) {
					this.pagination.cursor = page.split(
						'cursor'
					).pop().substring(1);
				}

				this.$emit('fetch');
			}
		}
	};
</script>
