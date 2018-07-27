<template>
	<div class="modal fade" id="branch-knowledge-dialog" tabindex="-1" role="dialog">
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
								<text-input v-model="form.branch_code" 
									:defaultValue="form.branch_code"
									:required="true"
									type="text"
									label="Branch code"
									name="branch_code"
									:editable="true"
									:focus="true"
									:hideLabel="false"
									:error="form.errors.get('branch_code')">
								</text-input>
							</div>
							<div class="col">
								<text-input v-model="form.product_type" 
									:defaultValue="form.product_type"
									:required="true"
									type="text"
									label="Product type"
									name="product_type"
									:editable="true"
									:focus="true"
									:hideLabel="false"
									:error="form.errors.get('product_type')">
								</text-input>
							</div>
						</div>
						<div class="row">
							<div class="col">
								<text-input v-model="form.zone_type" 
									:defaultValue="form.zone_type"
									:required="true"
									type="text"
									label="Default zone type"
									name="zone_type"
									:editable="true"
									:focus="true"
									:hideLabel="false"
									:error="form.errors.get('zone_type')">
								</text-input>
							</div>
							<div class="col">
								<text-input v-model="form.vendor_name" 
									:defaultValue="form.vendor_name"
									:required="true"
									type="text"
									label="Default vendor"
									name="vendor_name"
									:editable="true"
									:focus="true"
									:hideLabel="false"
									:error="form.errors.get('vendor_name')">
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
				selectedKnowledge: '',
				isEdit: false,
				branches: [],
				form: new Form({
					branch_code: '',
					product_type: '',
					zone_type: 'Domestic',
					vendor_name: ''
				})
			};
		},

		mounted() {
			window.events.$on('createBranchKnowledge', evt => this.createKnowledge(evt));
			window.events.$on('editBranchKnowledge', evt => this.editKnowledge(evt));

			$("#branch-knowledge-dialog").on("hide.bs.modal", function(e){
				this.closeDialog();
			}.bind(this));
		},

		methods: {
				
			createKnowledge(evt) {
				this.openDialog();
				
			},

			editKnowledge(evt) {
				this.selectedKnowledge = evt[0];
				this.isEdit = true;
				this.setForm();
				this.openDialog();
			},

			openDialog() {
				$("#branch-knowledge-dialog").modal();
				this.isActive = true;
			},

			closeDialog() {
				this.isActive = false;
				this.form.reset();
				this.selectedKnowledge = '';
			},

			setForm() {
				this.form.branch_code = this.selectedKnowledge.branch_code;
				this.form.product_type = this.selectedKnowledge.product_type;
				this.form.zone_type = this.selectedKnowledge.zone_type;
				this.form.vendor_name = this.selectedKnowledge.vendor_name;
			},

			submit() {
				this.form.post(this.url)
					.then(response => this.onSuccess(response));
			},

			onSuccess(response) {
				$("#branch-knowledge-dialog").modal('hide');

				this.closeDialog();

				window.events.$emit("reload-table");
			}
		},

		computed: {
			title() {
				return this.selectedKnowledge ? "Edit knowledge" : "Create knowledge";
			},

			action() {
				return this.form.submitting ? "<i class='fas fa-circle-notch fa-spin'></i>" : this.actionText;
			},

			actionText() {
				return this.selectedKnowledge ? "Update" : "Create";
			},

			url() {
				return this.selectedKnowledge ? "/admin/branch/knowledge/" + this.selectedKnowledge.id : "/admin/branch/knowledge";
			}
		}


	}
</script>