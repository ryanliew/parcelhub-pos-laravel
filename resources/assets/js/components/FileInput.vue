<template>
	<div class="form-group mb-3">
		<label>{{ label }} <span class="text-danger" v-if="required">*</span></label>
		<div class="input-group mb-2">
			<div class="custom-file">
			    <input type="file" :accept="accept" class="custom-file-input" :id="name" @change="onChange">
			    <label class="custom-file-label" :for="name">Select file</label>
			</div>
		</div>
	  	<span class="text-danger" v-if="error" v-text="error"></span><br>
	  	<!-- display -->

		<span v-if="defaultFile.name">
			<i class="fas fa-file-excel"></i> {{ defaultFile.name }}
		</span>

		<span v-if="defaultFile.file">
			<i class="fa fa-file-excel"></i> {{ defaultFile.file.name }}
		</span>

		
		
	 </div>
</template>

<script>
	export default {
		props: ['label', 'name', 'required', 'defaultFile', 'error', 'accept'],
		data() {
			return {
				
			};
		},
		methods: {
			onChange(e) {
				if( ! e.target.files.length ) return;

				let file = e.target.files[0];

				let reader = new FileReader();

				reader.readAsDataURL(file);

				reader.onload = e => {
					let src = e.target.result;
					
					flash("File selected: " + file.name);

					this.$emit('loaded', { src, file });
				};	
			}
		},

		computed: {
			fileName() {
				return this.defaultImage.name;
			}
		}	
	}
</script>