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
	        		<form @submit.prevent="submit" 
						@keydown="error = ''" 
						@input="error = ''">
						
						<div class="alert alert-danger" role="alert" v-if="error">
						  	{{ error }}
						</div>
						<text-input v-model="form.password" 
							:defaultValue="form.password"
							:required="true"
							type="password"
							label="Branch admin password"
							name="password"
							:editable="true"
							:focus="true"
							:hideLabel="false">
						</text-input>

						<textarea-input v-model="form.remarks" 
							:defaultValue="form.remarks"
							label="Cancel remarks"
							:required="true"
							:error="form.errors.get('remarks')"
							name="remarks"
							:editable="true"
							:focus="false"
							:hideLabel="false">
						</textarea-input>
					</form>
	      		</div>
	      		<div class="modal-footer">
	        		<button type="button" class="btn btn-danger" @click="submit" v-html="action"></button>
	        		<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
	      		</div>
	    	</div>
	  	</div>
	</div>
</template>

<script>
	export default {
		props: [],

		data() {
			return {
				form: new Form({
					remarks: '',
					password: '',
				}),

				error: '',
				data: ''
			};
		},

		mounted() {
			window.events.$on("cancelInvoice", evt => this.openDialog(evt));
		},

		methods: {
			openDialog(evt) {
				this.form.reset();
				this.error = '';
				this.data= evt[0];
				$("#cancel-dialog").modal();
				this.isActive = true;
			},

			closeDialog() {
				this.form.reset();
				this.error = '';
				this.isActive = false;
			},

			submit() {
				this.form.post("/invoices/cancel/" + this.data.id)
					.then(response => this.onSuccess(response));
			},

			onSuccess(response) {
				this.error = response.error;

				if(!response.error) {
					this.closeDialog();
					
					setTimeout(function(){
						location.reload();
					}, 2000);
				}
			}
		},

		computed: {
			action() {
				return this.form.submitting ? "<i class='fas fa-circle-notch fa-spin'></i>" : "Cancel invoice";
			},
		}	
	}
</script>