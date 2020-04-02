<template>
	<div>
		<div class="card" id="import-header">
			<div class="card-body">          
    			<input class="file-input" type="file" ref="file" name="file" @change="fileUploaded">
				<button class="btn btn-primary" @click="importFromExcel()" :disabled="!form.file">Import</button>
				<i v-if="processing" class="fa fa-spinner fa-spin fa-2x fa-fw"></i>
				<a href="/profit_and_loss.xlsx" target="_blank">
					<button class="btn btn-primary" style="float: right;" title="Download sample excel">Download</button>
				</a>
            </div>		
		</div>
	</div>
</template>

<script>
	export default {
		props: ["created_by"],
		data() {
			return {
				form: new Form({
					created_by: this.created_by,
					fileName: "",
					file: "",
					
				}),	
				processing: false,
			};
		},

		mounted() {
			
		},

		methods: {
			fileUploaded() {
				this.form.file = this.$refs.file.files[0];
			},

            importFromExcel() {		
				this.processing = true;
				let url = "profit_and_loss/import";
				this.form.post(url)
					.then(response => this.onSuccess(response))
					.catch(error => this.onError(error));
			},

			onSuccess(response) {
				console.log("Success");
				this.processing = false;
				window.events.$emit("reload-table");
			},

			onError(error) {	
				this.processing = false;			
			},		
		}		
	}
</script>