<template>
	<div class="modal fade" id="sku-importer-dialog" tabindex="-1" role="dialog">
	  	<div class="modal-dialog modal-lg" role="document">
	    	<div class="modal-content">
	      		<div class="modal-header">
	        		<h5 class="modal-title">Import SKU</h5>
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
	</div>
</template>

<script>
	export default {
		props: [''],
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
			window.events.$on('importProducts', evt => this.importProduct(evt));

			$("#sku-importer-dialog").on("hide.bs.modal", function(e){
				this.closeDialog();
			}.bind(this));
		},

		methods: {
			importProduct(evt) {
				this.openDialog();
				
			},

			openDialog() {
				$("#sku-importer-dialog").modal();
				this.isActive = true;
			},

			closeDialog() {
				this.isActive = false;
				this.selectedProduct = '';
				this.form.reset();
			},

			selectedFileChanged(e) {
				this.selectedFile = { src: e.src, file: e.file };
				this.form.file = e.file;
				this.form.errors.clear("file");
			},

			submit() {
				this.form.post(this.url)
					.then(response => this.onSuccess(response));
			},

			onSuccess(response) {
				$("#sku-importer-dialog").modal('hide');

				this.closeDialog();

				window.events.$emit("reload-table");
			}
		},

		computed: {
			action() {
				return this.form.submitting ? "<i class='fas fa-circle-notch fa-spin'></i>" : this.actionText;
			},

			actionText() {
				return "Import";
			},

			url() {
				return "/admin/products/import";
			}
		}
	}
</script>