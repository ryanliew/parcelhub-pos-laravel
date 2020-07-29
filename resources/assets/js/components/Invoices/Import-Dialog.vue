<template>
	<div class="modal fade" id="invoice-import-dialog" tabindex="-1" role="dialog">
	  	<div class="modal-dialog modal-lg" role="document">
	    	<div class="modal-content">
	      		<div class="modal-header">
	        		<h5 class="modal-title">Import Invoice</h5>
	        		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          			<span aria-hidden="true">&times;</span>
	        		</button>
	      		</div>
	      		<div class="modal-body">
	        		<form @submit.prevent="submit" 
						@keydown="form.errors.clear($event.target.name)" 
						@input="form.errors.clear($event.target.name)">
						
						<file-input v-model="selectedFile" :defaultFile="selectedFile"
							@loaded="selectedFileChanged"
							label="Select the Excel file"
							name="file"
							:required="true"
							accept=".xls, .xlsx"
							:error="form.errors.get('file')">
						</file-input>
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
		props: ['url'],

		mixins: [ConfirmationMixin],

		data() {
			return {
				isActive: false,
				selectedFile: {},
				isEdit: false,
				form: new Form({
					file: ''
				})
			};
		},

		mounted() {
			window.events.$on('importInvoice', evt => this.importInvoice(evt));

			$("#invoice-import-dialog").on("hide.bs.modal", function(e){
				this.closeDialog();
			}.bind(this));
		},

		methods: {
			importInvoice(evt) {
				this.openDialog();
				
			},

			openDialog() {
				$("#invoice-import-dialog").modal();
				this.isActive = true;
			},

			closeDialog() {
				this.isActive = false;
				this.selectedFile = '';
				this.form.reset();
			},

			selectedFileChanged(e) {
				this.selectedFile = { src: e.src, file: e.file };
				this.form.file = e.file;
				this.form.errors.clear("file");
			},

			submit() {
				this.isConfirming = true;
			},

			confirmSubmit() {
				this.isConfirming = false;
				let url = "/invoices/import";
				this.form.post(url)
					.then(response => this.onImportSuccess(response))
					.catch(error => this.onImportError(error));
			},
			
			onImportSuccess(response) {
				console.log("Success");
                if(!response.error) {
                    var loc = "/invoices/edit/" + response.invoice_id;
                    window.open(loc, '_blank');                    
					this.closeDialog();					
					setTimeout(function(){
						location.reload();
					}, 2000);
				}
			},
			
			onImportError(error) {
				this.selectedFile = "";
			}
		},

		computed: {
			action() {
				return this.form.submitting ? "<i class='fas fa-circle-notch fa-spin'></i>" : this.actionText;
			},

			actionText() {
				return "Import";
			},

			importUrl() {
				return this.url ? this.url : "/admin/products/import";
			}
		}
	}
</script>