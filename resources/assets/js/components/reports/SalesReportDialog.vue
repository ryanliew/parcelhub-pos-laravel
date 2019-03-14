<template>
	<div class="modal fade" id="cancel-dialog" tabindex="-1" role="dialog">
	  	<div class="modal-dialog modal-lg" role="document">
	    	<div class="modal-content">
	      		<div class="modal-header">
	        		<h5 class="modal-title">Cancel invoice</h5>
	        		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          			<span aria-hidden="true">&times;</span>
	        		</button>
	      		</div>
	      		<div class="modal-body">
	        		<form @submit.prevent="submit">
						<text-input v-model="from" 
							:defaultValue="from"
							:required="true"
							type="date"
							label="From date"
							name="password"
							:editable="true"
							:focus="true"
							:hideLabel="false">
						</text-input>

						<text-input v-model="to" 
							:defaultValue="to"
							:required="true"
							type="date"
							label="To date"
							name="password"
							:editable="true"
							:focus="true"
							:hideLabel="false">
						</text-input>
					</form>
	      		</div>
	      		<div class="modal-footer">
	        		<button type="button" class="btn btn-primary" @disabled="!canSubmit" @click="submit" v-html="action"></button>
	        		<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
	      		</div>
	    	</div>
	  	</div>
	</div>
</template>

<script>
	import moment from 'moment';
	export default {
		props: [],

		data() {
			return {
				from: moment().startOf('month').format('YYYY-MM-DD'),
				to: moment().format('YYYY-MM-DD')
			};
		},

		mounted() {
			window.events.$on("generateSalesReport", evt => this.openDialog(evt));
		},

		methods: {
			openDialog(evt) {
				$("#cancel-dialog").modal();
				this.isActive = true;
			},

			closeDialog() {
				this.isActive = false;
			},

			submit() {
				let url ="/admin/reports/sales?from=" + this.from + "&to=" + this.to;
				window.location.href = url;
			},
		},

		computed: {
			action() {
				return "Generate report";
			},

			canSubmit() {
				return this.from && this.to;
			}
		}	
	}
</script>