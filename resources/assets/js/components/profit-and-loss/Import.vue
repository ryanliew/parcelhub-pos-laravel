<template>
	<div>
		<div class="card" id="import-header">
			<div class="card-body">            
    			<input class="file-input" type="file" ref="file" name="file" @change="fileUploaded">
				<button @click="importFromExcel()" :disabled="!form.file">Import</button>
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
			};
		},

		mounted() {
		},

		methods: {
			fileUploaded() {
				this.form.file = this.$refs.file.files[0];
			},

            importFromExcel() {				
				let url = "profit_and_loss/import";
				this.form.post(url)
					.then(response => this.onSuccess(response))
					.catch(error => this.onError(error));
			},

			onSuccess(response) {
				console.log("Success");
				window.events.$emit("reload-table");
			},

			onError(error) {				
			},		
		}		
	}
</script>