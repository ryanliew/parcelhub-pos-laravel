<template>
	<div class="modal fade" id="inventory-dialog" tabindex="-1" role="dialog">
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
								<text-input v-model="form.name" 
									:defaultValue="form.name"
									:required="true"
									type="text"
									label="Name"
									name="name"
									:editable="true"
									:focus="false"
									:hideLabel="false"
									:error="form.errors.get('name')">
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
	  	<confirmation :message="confirm_message" :secondary="secondary_message" :confirming="isConfirming" @cancel="isConfirming = false" @confirm="confirmSubmit"></confirmation>
	</div>
</template>

<script>
	import ConfirmationMixin from "../../mixins/ConfirmationMixin.js";
	export default {
		props: [''],

		mixins: [ConfirmationMixin],

		data() {
			return {
				isActive: false,
				selectedInventory: '',
                products: [],
				isEdit: false,
				isDelete: false,
				form: new Form({
					name: '',					
				})
			};
		},

		mounted() {
			window.events.$on('createInventory', evt => this.createInventory(evt));
			window.events.$on('editInventory', evt => this.editInventory(evt));
			window.events.$on('deleteInventory', evt => this.deleteInventory(evt));

			$("#inventory-dialog").on("hide.bs.modal", function(e){
				this.closeDialog();
			}.bind(this));
		},

		methods: {	
			createInventory(evt) {
				this.openDialog();				
			},

			editInventory(evt) {
				this.selectedInventory = evt[0];
				this.isEdit = true;
				this.setForm();
				this.openDialog();
			},

			openDialog() {
				$("#inventory-dialog").modal();
				this.isActive = true;
			},

			closeDialog() {
				this.isActive = false;
                this.selectedInventory = '';
				this.form.reset();
				this.isEdit = false;
			},

			setForm() {
				this.form.name = this.selectedInventory.name;	
			},

			submit() {
				this.isConfirming = true;
			},

			confirmSubmit() {
				this.isConfirming = false;
				this.form.post(this.url)
					.then(response => this.onSuccess(response));
			},

			onSuccess(response) {
				$("#inventory-dialog").modal('hide');

				this.closeDialog();

				window.events.$emit("reload-table");
			},

			deleteInventory(evt) {
				this.selectedInventory= evt[0];
				this.isDelete = true;
				this.isConfirming = true;
			},
		},

		computed: {
			title() {
				return this.selectedInventory ? "Edit Inventory - " + this.selectedInventory.name : "Create Inventory";
			},

			action() {
				return this.form.submitting ? "<i class='fas fa-circle-notch fa-spin'></i>" : this.actionText;
			},

			actionText() {
				return this.selectedInventory ? "Update" : "Create";
			},

			url() {
				if(!this.isDelete) {
					return this.selectedInventory ? "/admin/inventory/" + this.selectedInventory.id : "/admin/inventory";
				}
				else {
					return "/admin/inventory/" + this.selectedInventory.id + "/delete";
				}			
			}
		},

		watch: {
		}
	}
</script>