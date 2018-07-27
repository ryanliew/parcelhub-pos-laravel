<template>
	<div class="modal fade" id="customer-statement" tabindex="-1" role="dialog">
	  	<div class="modal-dialog modal-lg" role="document">
	    	<div class="modal-content">
	      		<div class="modal-header">
	        		<h5 class="modal-title">{{ title }}</h5>
	        		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          			<span aria-hidden="true">&times;</span>
	        		</button>
	      		</div>
	      		<div class="modal-body">
	        		<form @submit.prevent="submit" 
						@keydown="form.errors.clear($event.target.name)" 
						@input="form.errors.clear($event.target.name)">
						<div class="row">
							<div class="col">
								<text-input v-model="form.date_from" 
									:defaultValue="form.date_from"
									:required="true"
									type="date"
									label="Date from:"
									name="date_from"
									:editable="true"
									:focus="false"
									:hideLabel="false"
									:error="form.errors.get('date_from')">
								</text-input>
							</div>
							<div class="col">
								<text-input v-model="form.date_to" 
									:defaultValue="form.date_to"
									:required="true"
									type="date"
									label="Date to:"
									name="date_to"
									:editable="true"
									:focus="false"
									:hideLabel="false"
									:error="form.errors.get('date_to')">
								</text-input>
							</div>
						</div>
					</form>
	      		</div>
	      		<div class="modal-footer">
	        		<button type="button" class="btn btn-primary" @click="submit" v-html="action"></button>
	        		<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
	      		</div>
	    	</div>
	  	</div>
	</div>
</template>

<script>
	export default {
		props: {
		    data: {
		        type: Object
		    }
		  },
		data() {
			return {
				form: new Form({
					date_to: '',
					date_from: '',
				}),

				selected_customer:'',
			};
		},

		mounted() {

			window.events.$on('generateStatement', evt => this.generateStatement(evt));

			$("#customer-statement").on("hide.bs.modal", function(e){
				this.closeDialog();
			}.bind(this));
		},
 
		methods: {
			generateStatement(evt) {
				this.selected_customer = evt[0];
				this.openDialog();
			},


			openDialog() {
				$("#customer-statement").modal();
				this.isActive = true;
			},

			closeDialog() {
				this.isActive = false;
				this.form.reset();
			},


			submit() {
				this.form.post(this.url)
					.then(response => this.onSuccess(response));
			},

			onSuccess(response) {

				$("#customer-statement").modal('hide');

				this.closeDialog();

				window.open("/customers/statement/" + response.id + '/' + response.start + '/' + response.end, '_blank');

			},

		},

		computed: {
			title() {
				return "Generate account of statement";
			},

			action() {
				return this.form.submitting ? "<i class='fas fa-circle-notch fa-spin'></i>" : this.actionText;
			},

			actionText() {
				return "Generate statement";
			},

			url() {
				return "/customers/statement/" + this.selected_customer.id;
			},

		},

	}
</script>