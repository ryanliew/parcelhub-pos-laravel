<template>
	<div class="modal fade" id="tax-dialog" tabindex="-1" role="dialog">
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
								<text-input v-model="form.code" 
									:defaultValue="form.code"
									:required="true"
									type="text"
									label="Tax code"
									name="code"
									:editable="true"
									:focus="true"
									:hideLabel="false"
									:error="form.errors.get('code')">
								</text-input>
							</div>
							<div class="col">
								<text-input v-model="form.percentage" 
									:defaultValue="form.percentage"
									:required="true"
									type="number"
									label="Percentage"
									name="percentage"
									:editable="true"
									:focus="true"
									:hideLabel="false"
									:error="form.errors.get('percentage')">
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
		props: [''],
		data() {
			return {
				isActive: false,
				selectedTax: '',
				isEdit: false,
				form: new Form({
					code: '',
					percentage: ''
				})
			};
		},

		mounted() {
			window.events.$on('createTax', evt => this.createTax(evt));
			window.events.$on('editTax', evt => this.editTax(evt));

			$("#tax-dialog").on("hide.bs.modal", function(e){
				this.closeDialog();
			}.bind(this));
		},

		methods: {
			createTax(evt) {
				this.openDialog();
				
			},

			editTax(evt) {
				this.selectedTax = evt[0];
				this.isEdit = true;
				this.setForm();
				this.openDialog();
			},

			openDialog() {
				$("#tax-dialog").modal();
				this.isActive = true;
			},

			closeDialog() {
				this.isActive = false;
				this.form.reset();
				this.selectedTax = '';
			},

			setForm() {
				this.form.code = this.selectedTax.code; 
				this.form.percentage = this.selectedTax.percentage;
			},

			submit() {
				this.form.post(this.url)
					.then(response => this.onSuccess(response));
			},

			onSuccess(response) {
				$("#tax-dialog").modal('hide');

				this.closeDialog();

				window.events.$emit("reload-table");
			}
		},

		computed: {
			title() {
				return this.selectedTax ? "Edit tax - " + this.selectedTax.name : "Create tax";
			},

			action() {
				return this.form.submitting ? "<i class='fas fa-circle-notch fa-spin'></i>" : this.actionText;
			},

			actionText() {
				return this.selectedTax ? "Update" : "Create";
			},

			url() {
				return this.selectedTax ? "/admin/taxes/" + this.selectedTax.id : "/admin/taxes";
			}
		},


	}
</script>