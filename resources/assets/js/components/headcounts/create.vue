<template>
	<div class="headcounts">
		<head v-for="headcount in headcounts"
			@selected="selectHead(headcount)">

		</head>
	</div>
</template>
<script>
	export default {
		props: [],

		data() { 
			return {
				headcounts: [],
			};
		},

		methods: {
			getHeadcounts() {
				axios.get("/data/headcounts/active")
					.then(response => this.setHeadcounts(response))
					.catch(error => this.catchAjaxError(error));
			},

			setHeadcounts(response) {
				this.headcounts = response.data;
			},

		}
	}
</script>