<template>
	<div class="modal fade" id="import-dialog" tabindex="-1" role="dialog">
	  	<div class="modal-dialog modal-lg" role="document">
	    	<div class="modal-content">
	      		<div class="modal-header">
	        		<h5 class="modal-title">Import invoice</h5>
	        		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          			<span aria-hidden="true">&times;</span>
	        		</button>
	      		</div>
	      		<div class="modal-body">
	        		<form @submit.prevent="submit">	
                        <div class="card" id="invoice-import-header">
                            <div class="card-body">
                                <div class="row">
                                    <input class="file-input" type="file" ref="file" name="file" @change="fileUploaded">
                                </div>
                            </div>
                        </div>
					</form>
	      		</div>

	      		<div class="modal-footer">
	        		<button type="button" class="btn btn-danger" @click="importFromExcel" v-html="action"></button>
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
					file: "",
				}),				
			};
		},

		mounted() {
			window.events.$on("importInvoice", evt => this.openDialog(evt));
		},

		methods: {
			openDialog(evt) {
				this.form.reset();				
                $("#import-dialog").modal();	
                this.isActive = true;			
			},

			closeDialog() {
                this.form.reset();
                this.isActive = false;
            },            
            
			fileUploaded() {
				this.form.file = this.$refs.file.files[0];
			},

            importFromExcel() {		
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
			},
		},

		computed: {
			action() {
				return this.form.submitting ? "<i class='fas fa-circle-notch fa-spin'></i>" : "Process invoice";
			},
		}	
	}
</script>